<?php

declare(strict_types=1);

namespace App\Controller\Flashcard;

use App\Attribute\RelativeToEntity;
use App\Controller\AbstractRestController;
use App\Entity\Flashcard;
use App\Entity\User;
use App\Enum\CountCriteria\FlashcardCountCriteria;
use App\Repository\FlashcardRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
#[RelativeToEntity(Flashcard::class)]
class FlashcardScalarController extends AbstractRestController
{
    #[Route('/flashcards/count/{criteria}', name: 'flashcard_count', methods: ['GET'])]
    public function countFlashcards(
        FlashcardRepository $flashcardRepository,
        #[CurrentUser] User $user,
        FlashcardCountCriteria $criteria = FlashcardCountCriteria::ALL,
    ): JsonResponse {
        $count = match ($criteria) {
            FlashcardCountCriteria::ALL => $flashcardRepository->countAll($user),
            FlashcardCountCriteria::TO_REVIEW => $flashcardRepository->countFlashcardsToReview($user),
            FlashcardCountCriteria::CORRECT => $flashcardRepository->countCorrectFlashcards($user),
        };

        return $this->json($count);
    }

    #[Route('/flashcards/averageGrade', name: 'flashcard_average_grade', methods: ['GET'])]
    public function getAverageGrade(
        FlashcardRepository $flashcardRepository,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $count = $flashcardRepository->averageGrade($user);

        return $this->json($count);
    }
}
