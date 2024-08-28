<?php

declare(strict_types=1);

namespace App\SpacedRepetition;

use App\Entity\Flashcard;
use App\Enum\GradeType;
use App\Enum\StateType;

class Fsrs4Algorithm extends AbstractSpacedRepetitionAlgorithm
{
    /**
     * @var float[]
     */
    protected array $w = [0.4, 0.6, 2.4, 5.8, 4.93, 0.94, 0.86, 0.01, 1.49, 0.14, 0.94, 2.18, 0.05, 0.34, 1.26, 0.29, 2.61];

    protected float $requestRetention = 0.9;

    protected float $decay = -1;

    protected float $factor = 1 / 9;

    public function getStability(Flashcard $flashcard, GradeType $grade): float
    {
        return match ($flashcard->getState()) {
            StateType::New => $this->initStability($grade),
            StateType::Learning => $this->nextStability($flashcard, $grade),
        };
    }

    public function getDifficulty(Flashcard $flashcard, GradeType $grade): float
    {
        return match ($flashcard->getState()) {
            StateType::New => $this->initDifficulty($grade),
            StateType::Learning => $this->nextDifficulty($flashcard, $grade),
        };
    }

    public function getInterval(Flashcard $flashcard, GradeType $grade): int
    {
        $interval = ($flashcard->getStability() / $this->factor) * (($this->requestRetention ** (1 / $this->decay)) - 1);

        return min(max((int) round($interval), 1), self::MAX_INTERVAL);
    }

    protected function initStability(GradeType $grade): float
    {
        return $this->w[$grade->value - 1];
    }

    protected function getRetrievability(Flashcard $flashcard): float
    {
        $elapsedDays = (int) $flashcard->getPreviousReview()->diff(new \DateTimeImmutable())->format('%a');

        return (1 + $this->factor * ($elapsedDays / $flashcard->getStability())) ** $this->decay;
    }

    protected function nextStability(Flashcard $flashcard, GradeType $grade): float
    {
        if ($grade->isCorrect()) {
            return $this->nextRecallStability($flashcard, $grade);
        }

        return $this->nextForgetStability($flashcard);
    }

    protected function initDifficulty(GradeType $grade): float
    {
        $difficulty = $this->w[4] - ($grade->value - 3) * $this->w[5];

        return min(max($difficulty, 1), 10);
    }

    protected function nextDifficulty(Flashcard $flashcard, GradeType $grade): float
    {
        $D = $flashcard->getDifficulty();
        $G = $grade->value;

        $difficulty = $this->w[7] * $this->initDifficulty(GradeType::GOOD) + (1 - $this->w[7]) * ($D - $this->w[6] * ($G - 3));

        return min(max($difficulty, 1), 10);
    }

    private function nextRecallStability(Flashcard $flashcard, GradeType $grade): float
    {
        $hardPenalty = $grade === GradeType::HARD ? $this->w[15] : 1;
        $easyPenalty = $grade === GradeType::EASY ? $this->w[16] : 1;

        $S = $flashcard->getStability();
        $D = $flashcard->getDifficulty();
        $R = $this->getRetrievability($flashcard);

        return $S * (\M_E ** $this->w[8] * (11 - $D) * $S ** (-$this->w[9]) * (\M_E ** ($this->w[10] * (1 - $R)) - 1) * $hardPenalty * $easyPenalty + 1);
    }

    private function nextForgetStability(Flashcard $flashcard): float
    {
        $S = $flashcard->getStability();
        $D = $flashcard->getDifficulty();
        $R = $this->getRetrievability($flashcard);

        return $this->w[11] * $D ** (-$this->w[12]) * (($S + 1) ** $this->w[13] - 1) * \M_E ** ($this->w[14] * (1 - $R));
    }
}
