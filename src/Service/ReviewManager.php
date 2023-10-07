<?php

namespace App\Service;

use App\Entity\Unit;
use App\Entity\User;
use App\Entity\Topic;
use App\Entity\Review;
use App\Enum\GradeType;
use App\Entity\Flashcard;
use App\Repository\ReviewRepository;
use App\Repository\FlashcardRepository;

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

    public function resetFlashcard(Flashcard $flashcard, User $user)
    {
        $this->reviewRepository->resetBy($flashcard, $user);
        $this->flashcardRepository->resetBy($flashcard, $user);
    }

    public function resetFlashcards(Unit|Topic $group, User $user)
    {
        $this->reviewRepository->resetBy($group, $user);
        $this->flashcardRepository->resetBy($group, $user);
    }

    public function resetAllFlashcards(User $user)
    {
        $this->reviewRepository->resetAll($user);
        $this->flashcardRepository->resetAll($user);
    }
}
