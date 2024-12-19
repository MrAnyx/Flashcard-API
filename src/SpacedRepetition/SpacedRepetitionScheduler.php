<?php

declare(strict_types=1);

namespace App\SpacedRepetition;

use App\Entity\Flashcard;
use App\Enum\GradeType;
use App\Enum\StateType;

class SpacedRepetitionScheduler
{
    public function review(Flashcard &$flashcard, GradeType $grade, AbstractSpacedRepetitionAlgorithm $algorithm): void
    {
        $now = new \DateTimeImmutable();

        if ($flashcard->getNextReview() > $now) {
            return;
        }

        $flashcard->setStability($algorithm->getStability($flashcard, $grade));
        $flashcard->setDifficulty($algorithm->getDifficulty($flashcard, $grade));

        $interval = $algorithm->getInterval($flashcard, $grade);

        // TODO Handle timezone
        $flashcard->setNextReview($now->modify("+{$interval} days")->setTime(0, 0, 0));
        $flashcard->setPreviousReview($now);
        $flashcard->setState(StateType::Learning);
    }
}
