<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Review;
use App\Enum\GradeType;
use App\Entity\Flashcard;

class ReviewManager
{
    public function __construct(
    ) {
    }

    public function createReview(Flashcard $flashcard, User $user, GradeType $grade)
    {
        // Create the new review element
        $review = new Review();
        $review
            ->setFlashcard($flashcard)
            ->setUser($user)
            ->setGrade($grade);

        return $review;
    }
}
