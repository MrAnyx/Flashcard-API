<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Flashcard;
use App\Enum\GradeType;
use App\Enum\StateType;
use App\Utility\Random;

class SpacedRepetitionScheduler
{
    public const SESSION_SIZE = 20;

    public const REQUEST_RETENTION = 0.9;

    public const MAX_INTERVAL = 36500;

    public const W = [0.4, 0.6, 2.4, 5.8, 4.93, 0.94, 0.86, 0.01, 1.49, 0.14, 0.94, 2.18, 0.05, 0.34, 1.26, 0.29, 2.61];

    public function review(Flashcard &$flashcard, GradeType $grade): void
    {
        if ($flashcard->getNextReview() > (new \DateTimeImmutable())) {
            return;
        }

        if ($flashcard->getState() === StateType::New) {
            $flashcard->setStability($this->initStability($grade));
            $flashcard->setDifficulty($this->initDifficulty($grade));
        } else {
            $flashcard->setStability($this->nextStability($flashcard, $grade));
            $flashcard->setDifficulty($this->nextDifficulty($flashcard, $grade));
        }

        $interval = $this->nextInterval($flashcard);
        $flashcard->setNextReview((new \DateTimeImmutable())->modify("+{$interval} days")->setTime(0, 0, 0));
        $flashcard->setPreviousReview(new \DateTimeImmutable());
        $flashcard->setState(StateType::Learning);
    }

    private function initStability(GradeType $grade): float
    {
        return self::W[$grade->value - 1];
    }

    private function initDifficulty(GradeType $grade): float
    {
        $difficulty = self::W[4] - ($grade->value - 3) * self::W[5];

        return min(max($difficulty, 1), 10);
    }

    private function nextDifficulty(Flashcard $flashcard, GradeType $grade): float
    {
        $D = $flashcard->getDifficulty();
        $G = $grade->value;

        $difficulty = self::W[7] * $this->initDifficulty(GradeType::GOOD) + (1 - self::W[7]) * ($D - self::W[6] * ($G - 3));

        return min(max($difficulty, 1), 10);
    }

    private function getRetrievability(Flashcard $flashcard): float
    {
        $elapsedDays = (int) $flashcard->getPreviousReview()->diff(new \DateTimeImmutable())->format('%a');

        return (1 + ($elapsedDays / (9 * $flashcard->getStability()))) ** (-1);
    }

    private function nextInterval(Flashcard $flashcard): int
    {
        $interval = 9 * $flashcard->getStability() * ((1 / self::REQUEST_RETENTION) - 1);

        // To prevent card that are review are the same date to appear at the same day every time
        // $interval *= Random::getFloat(0.95, 1.05);

        return min(max((int) round($interval), 1), self::MAX_INTERVAL);
    }

    private function nextRecallStability(Flashcard $flashcard, GradeType $grade): float
    {
        $hardPenalty = $grade === GradeType::HARD ? self::W[15] : 1;
        $easyPenalty = $grade === GradeType::EASY ? self::W[16] : 1;

        $S = $flashcard->getStability();
        $D = $flashcard->getDifficulty();
        $R = $this->getRetrievability($flashcard);

        return $S * (\M_E ** self::W[8] * (11 - $D) * $S ** (-self::W[9]) * (\M_E ** (self::W[10] * (1 - $R)) - 1) * $hardPenalty * $easyPenalty + 1);
    }

    private function nextForgetStability(Flashcard $flashcard): float
    {
        $S = $flashcard->getStability();
        $D = $flashcard->getDifficulty();
        $R = $this->getRetrievability($flashcard);

        return self::W[11] * $D ** (-self::W[12]) * (($S + 1) ** self::W[13] - 1) * \M_E ** (self::W[14] * (1 - $R));
    }

    private function nextStability(Flashcard $flashcard, GradeType $grade): float
    {
        if ($grade->isCorrect()) {
            return $this->nextRecallStability($flashcard, $grade);
        }

        return $this->nextForgetStability($flashcard);
    }
}
