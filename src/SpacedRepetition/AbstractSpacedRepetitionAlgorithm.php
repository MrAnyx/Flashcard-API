<?php

declare(strict_types=1);

namespace App\SpacedRepetition;

use App\Entity\Flashcard;
use App\Enum\GradeType;
use App\Enum\StateType;

abstract class AbstractSpacedRepetitionAlgorithm
{
    protected const MAX_INTERVAL = 36500;

    /**
     * @return float[]
     */
    abstract protected function getWeights(): array;

    abstract protected function initStability(GradeType $grade): float;

    abstract protected function initDifficulty(GradeType $grade): float;

    abstract protected function nextDifficulty(Flashcard $flashcard, GradeType $grade): float;

    abstract protected function getRetrievability(Flashcard $flashcard): float;

    abstract protected function nextInterval(Flashcard $flashcard): int;

    abstract protected function nextRecallStability(Flashcard $flashcard, GradeType $grade): float;

    abstract protected function nextForgetStability(Flashcard $flashcard): float;

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

    protected function nextStability(Flashcard $flashcard, GradeType $grade): float
    {
        if ($grade->isCorrect()) {
            return $this->nextRecallStability($flashcard, $grade);
        }

        return $this->nextForgetStability($flashcard);
    }
}
