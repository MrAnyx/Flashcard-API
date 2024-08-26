<?php

declare(strict_types=1);

namespace App\SpacedRepetition;

use App\Entity\Flashcard;
use App\Enum\GradeType;

abstract class AbstractSpacedRepetitionAlgorithm
{
    public const MAX_INTERVAL = 36500; // 100 years

    abstract public function getStability(Flashcard $flashcard, GradeType $grade): float;

    abstract public function getDifficulty(Flashcard $flashcard, GradeType $grade): float;

    abstract public function getInterval(Flashcard $flashcard, GradeType $grade): int;
}
