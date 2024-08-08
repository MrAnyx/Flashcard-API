<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Flashcard;
use App\Entity\Review;
use App\Entity\Topic;
use App\Entity\Unit;
use App\Entity\User;
use App\Enum\GradeType;
use App\Repository\FlashcardRepository;
use App\Repository\ReviewRepository;

class ReviewManager
{
    public function __construct(
        private ReviewRepository $reviewRepository,
        private FlashcardRepository $flashcardRepository
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

    public function resetFlashcard(Flashcard $flashcard, User $user): void
    {
        $this->reviewRepository->resetBy($user, $flashcard);
        $this->flashcardRepository->resetBy($user, $flashcard);
    }

    public function resetFlashcards(Unit|Topic $group, User $user): void
    {
        $this->reviewRepository->resetBy($user, $group);
        $this->flashcardRepository->resetBy($user, $group);
    }

    public function resetAllFlashcards(User $user): void
    {
        $this->reviewRepository->resetBy($user);
        $this->flashcardRepository->resetBy($user);
    }
}
