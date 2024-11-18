<?php

declare(strict_types=1);

namespace App\Controller\Unit;

use App\Attribute\Resource;
use App\Controller\AbstractRestController;
use App\Entity\Session;
use App\Entity\Unit;
use App\Entity\User;
use App\Enum\SettingName;
use App\Repository\FlashcardRepository;
use App\Repository\ReviewRepository;
use App\Utility\Regex;
use App\Voter\UnitVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
class UnitBehaviorController extends AbstractRestController
{
    #[Route('/units/{id}/reset', name: 'reset_unit', methods: ['PATCH'], requirements: ['id' => Regex::INTEGER])]
    public function resetUnit(
        ReviewRepository $reviewRepository,
        FlashcardRepository $flashcardRepository,
        #[Resource(UnitVoter::OWNER)] Unit $unit,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $reviewRepository->resetBy($user, $unit);
        $flashcardRepository->resetBy($user, $unit);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/units/{id}/session', name: 'session_unit', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getFlashcardSession(
        FlashcardRepository $flashcardRepository,
        EntityManagerInterface $em,
        #[Resource(UnitVoter::OWNER)] Unit $unit,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $cardsToReview = $flashcardRepository->findFlashcardToReviewBy($unit, $user, $this->getUserSetting(SettingName::FLASHCARD_PER_SESSION));

        if (\count($cardsToReview) === 0) {
            return $this->json([
                'session' => null,
                'flashcards' => [],
            ]);
        }

        shuffle($cardsToReview);

        $session = new Session();
        $session->setAuthor($user);
        $this->validateEntity($session);
        $em->persist($session);
        $em->flush();

        return $this->json([
            'session' => $session,
            'flashcards' => $cardsToReview,
        ], context: ['groups' => ['read:flashcard:user', 'read:session:user']]);
    }
}
