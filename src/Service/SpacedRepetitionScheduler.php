<?php

namespace App\Service;

use App\Enum\GradeType;
use App\Entity\Flashcard;

class SpacedRepetitionScheduler
{
    public const W = [0.4, 0.6, 2.4, 5.8, 4.93, 0.94, 0.86, 0.01, 1.49, 0.14, 0.94, 2.18, 0.05, 0.34, 1.26, 0.29, 2.61];

    public function review(Flashcard $flashcard, GradeType $grade): Flashcard
    {
        // Algorithm based on the FSRS v4 https://github.com/open-spaced-repetition/fsrs4anki/wiki/The-Algorithm#fsrs-v4

        if ($flashcard->countReviews() === 0) { // First review
            $flashcard
                ->setStability($this->calculateInitialStability($grade))
                ->setDifficulty($this->calculateInitialDifficulty($grade));
        } else { // other reviews
            $flashcard
                ->setDifficulty(self::W[7] * $this->calculateInitialDifficulty(GradeType::GOOD) + (1 - self::W[7]) * ($flashcard->getDifficulty() - self::W[6] * ($grade->value - 3)));
        }

        return $flashcard;
    }

    private function calculateInitialStability(GradeType $grade): float
    {
        return self::W[$grade->value - 1];
    }

    private function calculateInitialDifficulty(GradeType $grade): float
    {
        return self::W[4] - ($grade->value - 3) * self::W[5];
    }
}
