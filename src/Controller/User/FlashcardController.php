<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Attribute\Body;
use App\Attribute\RelativeToEntity;
use App\Attribute\Resource;
use App\Controller\AbstractRestController;
use App\Entity\Flashcard;
use App\Entity\Review;
use App\Entity\Session;
use App\Entity\Unit;
use App\Entity\User;
use App\Enum\CountCriteria\FlashcardCountCriteria;
use App\Enum\SettingName;
use App\Model\Filter;
use App\Model\Page;
use App\OptionsResolver\FlashcardOptionsResolver;
use App\OptionsResolver\SpacedRepetitionOptionsResolver;
use App\Repository\FlashcardRepository;
use App\Repository\ReviewRepository;
use App\SpacedRepetition\Fsrs4_5Algorithm;
use App\SpacedRepetition\SpacedRepetitionScheduler;
use App\Utility\Regex;
use App\ValueResolver\BodyResolver;
use App\Voter\FlashcardVoter;
use App\Voter\UnitVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
// #[IsGranted('IS_AUTHENTICATED', exceptionCode: 450)] --> Marche pas
class FlashcardController extends AbstractRestController
{
    #[Route('/flashcards', name: 'get_flashcards', methods: ['GET'])]
    public function getFlashcards(
        FlashcardRepository $flashcardRepository,
        #[RelativeToEntity(Flashcard::class)] Page $page,
        #[RelativeToEntity(Flashcard::class)] Filter $filter,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $flashcards = $flashcardRepository->paginateAndFilterAll($page, $filter, $user);

        return $this->jsonStd($flashcards, context: ['groups' => ['read:flashcard:user']]);
    }

    #[Route('/flashcards/{id}', name: 'get_flashcard', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getFlashcard(
        #[Resource(FlashcardVoter::OWNER)] Flashcard $flashcard,
    ): JsonResponse {
        return $this->jsonStd($flashcard, context: ['groups' => ['read:flashcard:user']]);
    }

    #[Route('/flashcards', name: 'create_flashcard', methods: ['POST'])]
    public function createFlashcard(
        EntityManagerInterface $em,
        FlashcardOptionsResolver $flashcardOptionsResolver,
        #[Body, ValueResolver(BodyResolver::class)] mixed $body,
    ): JsonResponse {
        try {
            // Validate the content of the request body
            $data = $flashcardOptionsResolver
                ->configureFront(true)
                ->configureBack(true)
                ->configureDetails(true)
                ->configureUnit(true)
                ->configureFavorite(true)
                ->configureHelp(true)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        $this->denyAccessUnlessGranted(UnitVoter::OWNER, $data['unit'], 'You can not use this resource');

        // Temporarly create the flashcard
        $flashcard = new Flashcard();
        $flashcard
            ->setFront($data['front'])
            ->setBack($data['back'])
            ->setDetails($data['details'])
            ->setUnit($data['unit'])
            ->setFavorite($data['favorite'])
            ->setHelp($data['help']);

        // Second validation using the validation constraints
        $this->validateEntity($flashcard);

        // Save the new flashcard
        $em->persist($flashcard);
        $em->flush();

        // Return the flashcard with the the status 201 (Created)
        return $this->jsonStd(
            $flashcard,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('api_get_flashcard', ['id' => $flashcard->getId()])],
            ['groups' => ['read:flashcard:user']]
        );
    }

    #[Route('/flashcards/{id}', name: 'delete_flashcard', methods: ['DELETE'], requirements: ['id' => Regex::INTEGER])]
    public function deleteFlashcard(
        EntityManagerInterface $em,
        #[Resource(FlashcardVoter::OWNER)] Flashcard $flashcard,
    ): JsonResponse {
        // Remove the flashcard
        $em->remove($flashcard);
        $em->flush();

        // Return a response with status 204 (No Content)
        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/flashcards/{id}', name: 'update_flashcard', methods: ['PATCH', 'PUT'], requirements: ['id' => Regex::INTEGER])]
    public function updateFlashcard(
        EntityManagerInterface $em,
        Request $request,
        FlashcardOptionsResolver $flashcardOptionsResolver,
        #[Resource(FlashcardVoter::OWNER)] Flashcard $flashcard,
        #[Body, ValueResolver(BodyResolver::class)] mixed $body,
    ): JsonResponse {
        try {
            // Check if the request method is PUT. In this case, all parameters must be provided in the request body.
            // Otherwise, all parameters are optional.
            $mandatoryParameters = $request->getMethod() === 'PUT';

            // Validate the content of the request body
            $data = $flashcardOptionsResolver
                ->configureFront($mandatoryParameters)
                ->configureBack($mandatoryParameters)
                ->configureDetails($mandatoryParameters)
                ->configureUnit($mandatoryParameters)
                ->configureFavorite($mandatoryParameters)
                ->configureHelp($mandatoryParameters)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        // Update each fields if necessary
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'front':
                    $flashcard->setFront($value);
                    break;
                case 'back':
                    $flashcard->setBack($value);
                    break;
                case 'details':
                    $flashcard->setDetails($value);
                    break;
                case 'unit':
                    $this->denyAccessUnlessGranted(UnitVoter::OWNER, $value, 'You can not use this resource');
                    $flashcard->setUnit($value);
                    break;
                case 'favorite':
                    $flashcard->setFavorite($value);
                    break;
                case 'help':
                    $flashcard->setHelp($value);
                    break;
            }
        }

        // Second validation using the validation constraints
        $this->validateEntity($flashcard);

        // Save the flashcard information
        $em->flush();

        // Return the flashcard
        return $this->jsonStd($flashcard, context: ['groups' => ['read:flashcard:user']]);
    }

    #[Route('/units/{id}/flashcards', name: 'get_flashcards_by_unit', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getFlashcardsByUnit(
        FlashcardRepository $flashcardRepository,
        #[RelativeToEntity(Flashcard::class)] Page $page,
        #[RelativeToEntity(Flashcard::class)] Filter $filter,
        #[Resource(UnitVoter::OWNER)] Unit $unit,
    ): JsonResponse {
        $flashcards = $flashcardRepository->paginateAndFilterByUnit($page, $filter, $unit);

        return $this->jsonStd($flashcards, context: ['groups' => ['read:flashcard:user']]);
    }

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

    #[Route('/flashcards/count', name: 'flashcard_count', methods: ['GET'])]
    public function countFlashcards(
        FlashcardRepository $flashcardRepository,
        Request $request,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $criteria = $this->getCountCriteria($request, FlashcardCountCriteria::class, FlashcardCountCriteria::ALL->value);

        $count = match ($criteria) {
            FlashcardCountCriteria::ALL => $flashcardRepository->countAll($user),
            FlashcardCountCriteria::TO_REVIEW => $flashcardRepository->countFlashcardsToReview($user),
            FlashcardCountCriteria::CORRECT => $flashcardRepository->countCorrectFlashcards($user),
        };

        return $this->jsonStd($count);
    }

    #[Route('/flashcards/averageGrade', name: 'flashcard_average_grade', methods: ['GET'])]
    public function getAverageGrade(
        FlashcardRepository $flashcardRepository,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $count = $flashcardRepository->averageGrade($user);

        return $this->jsonStd($count);
    }
}
