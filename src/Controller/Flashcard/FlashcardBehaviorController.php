<?php

declare(strict_types=1);

namespace App\Controller\Flashcard;

use App\Attribute\Body;
use App\Attribute\Resource;
use App\Controller\AbstractRestController;
use App\Entity\Flashcard;
use App\Entity\Review;
use App\Entity\Session;
use App\Entity\User;
use App\Enum\SettingName;
use App\OptionsResolver\SpacedRepetitionOptionsResolver;
use App\Repository\FlashcardRepository;
use App\Repository\ReviewRepository;
use App\SpacedRepetition\Fsrs4_5Algorithm;
use App\SpacedRepetition\SpacedRepetitionScheduler;
use App\Utility\Regex;
use App\Voter\FlashcardVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
class FlashcardBehaviorController extends AbstractRestController
{
    #[Route('/flashcards/{id}/review', name: 'review_flashcard', methods: ['POST'], requirements: ['id' => Regex::INTEGER])]
    public function reviewFlashcard(
        EntityManagerInterface $em,
        SpacedRepetitionOptionsResolver $spacedRepetitionOptionsResolver,
        SpacedRepetitionScheduler $spacedRepetitionScheduler,
        #[Resource(FlashcardVoter::OWNER)] Flashcard $flashcard,
        #[Body] mixed $body,
    ): JsonResponse {
        if ($flashcard->getNextReview() > (new \DateTimeImmutable())) {
            throw new BadRequestHttpException(\sprintf('You can not review the flashcard with id %d yet. The next review is scheduled for %s', $flashcard->getId(), $flashcard->getNextReview()->format('jS \\of F Y')));
        }

        try {
            $data = $spacedRepetitionOptionsResolver
                ->configureGrade()
                ->configureSession()
                ->resolve($body);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        if ($data['session']->getEndedAt() !== null) {
            throw new BadRequestHttpException(\sprintf('The session with id %d ended at %s. You can not associate a new review with this session', $data['session']->getId(), $data['session']->getEndedAt()->format('jS \\of F Y')));
        }

        $spacedRepetitionScheduler->review($flashcard, $data['grade'], new Fsrs4_5Algorithm());
        $this->validateEntity($flashcard);

        $review = new Review();
        $review
            ->setFlashcard($flashcard)
            ->setGrade($data['grade'])
            ->setSession($data['session'])
            ->setDifficulty($flashcard->getDifficulty())
            ->setStability($flashcard->getStability());

        $this->validateEntity($review);
        $em->persist($review);
        $em->flush();

        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/flashcards/reset', name: 'reset_all_flashcard', methods: ['POST'])]
    public function resetFlashcards(
        FlashcardRepository $flashcardRepository,
        ReviewRepository $reviewRepository,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $reviewRepository->resetBy($user);
        $flashcardRepository->resetBy($user);

        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/flashcards/{id}/reset', name: 'reset_flashcard', methods: ['POST'], requirements: ['id' => Regex::INTEGER])]
    public function resetFlashcard(
        FlashcardRepository $flashcardRepository,
        ReviewRepository $reviewRepository,
        #[Resource(FlashcardVoter::OWNER)] Flashcard $flashcard,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $reviewRepository->resetBy($user, $flashcard);
        $flashcardRepository->resetBy($user, $flashcard);

        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/flashcards/session', name: 'session_flashcard', methods: ['GET'])]
    public function getFlashcardSession(
        FlashcardRepository $flashcardRepository,
        EntityManagerInterface $em,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $cardsToReview = $flashcardRepository->findFlashcardToReview($user, $this->getUserSetting(SettingName::FLASHCARD_PER_SESSION));

        if (\count($cardsToReview) === 0) {
            return $this->jsonStd([
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

        return $this->jsonStd([
            'session' => $session,
            'flashcards' => $cardsToReview,
        ], context: ['groups' => ['read:flashcard:user', 'read:session:user']]);
    }
}
