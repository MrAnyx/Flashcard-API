<?php

namespace App\Enum;

enum GradeType: int
{
    case AGAIN = 1; // Complete blackout
    case HARD = 2; // Correct response recalled with serious difficulty
    case GOOD = 3; // Correct response after a hesitation
    case EASY = 4; // Perfect response

    public function isCorrect(): bool
    {
        return match ($this) {
            self::EASY, self::GOOD, self::HARD => true,
            default => false
        };
    }
}
