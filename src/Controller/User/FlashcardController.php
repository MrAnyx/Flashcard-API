<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use App\Entity\Flashcard;
use App\Entity\Unit;
use App\Entity\User;
use App\Exception\ApiException;
use App\Exception\ExceptionCode;
use App\OptionsResolver\FlashcardOptionsResolver;
use App\OptionsResolver\SpacedRepetitionOptionsResolver;
use App\Repository\FlashcardRepository;
use App\Service\RequestPayloadService;
use App\Service\ReviewManager;
use App\Service\SpacedRepetitionScheduler;
use App\Utility\Regex;
use App\Voter\FlashcardVoter;
use App\Voter\UnitVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api', 'api_', format: 'json')]
// #[IsGranted('IS_AUTHENTICATED', exceptionCode: 450)] --> Marche pas
class FlashcardController extends AbstractRestController
{
    #[Route('/flashcards', name: 'get_flashcards', methods: ['GET'])]
    public function getAllFlashcards(Request $request, FlashcardRepository $flashcardRepository): JsonResponse
    {
        $pagination = $this->getPaginationParameter(Flashcard::class, $request);

        /** @var User $user */
        $user = $this->getUser();

        $flashcards = $flashcardRepository->findAllWithPagination($pagination, $user);

        return $this->jsonStd($flashcards, context: ['groups' => ['read:flashcard:user']]);
    }

    #[Route('/flashcards/{id}', name: 'get_flashcard', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getOneFlashcard(int $id): JsonResponse
    {
        $flashcard = $this->getResourceById(Flashcard::class, $id);

        $this->denyAccessUnlessGranted(FlashcardVoter::OWNER, $flashcard, 'You can not access this resource');

        return $this->jsonStd($flashcard, context: ['groups' => ['read:flashcard:user']]);
    }

    #[Route('/flashcards', name: 'create_flashcard', methods: ['POST'])]
    public function createFlashcard(
        Request $request,
        EntityManagerInterface $em,
        FlashcardOptionsResolver $flashcardOptionsResolver,
        RequestPayloadService $requestPayloadService
    ): JsonResponse {
        try {
            // Retrieve the request body
            $body = $requestPayloadService->getRequestPayload($request);

            // Validate the content of the request body
            $data = $flashcardOptionsResolver
                ->configureFront(true)
                ->configureBack(true)
                ->configureDetails(true)
                ->configureUnit(true)
                ->configureFavorite(true)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        $this->denyAccessUnlessGranted(UnitVoter::OWNER, $data['unit'], 'You can not use this resource');

        // Temporarly create the flashcard
        $flashcard = new Flashcard();
        $flashcard
            ->setFront($data['front'])
            ->setBack($data['back'])
            ->setDetails($data['details'])
            ->setUnit($data['unit'])
            ->setFavorite($data['favorite']);

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
    public function deleteFlashcard(int $id, EntityManagerInterface $em): JsonResponse
    {
        $flashcard = $this->getResourceById(Flashcard::class, $id);

        $this->denyAccessUnlessGranted(FlashcardVoter::OWNER, $flashcard, 'You can not delete this resource');

        // Remove the flashcard
        $em->remove($flashcard);
        $em->flush();

        // Return a response with status 204 (No Content)
        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/flashcards/{id}', name: 'update_flashcard', methods: ['PATCH', 'PUT'], requirements: ['id' => Regex::INTEGER])]
    public function updateFlashcard(
        int $id,
        EntityManagerInterface $em,
        RequestPayloadService $requestPayloadService,
        Request $request,
        FlashcardOptionsResolver $flashcardOptionsResolver
    ): JsonResponse {
        $flashcard = $this->getResourceById(Flashcard::class, $id);

        $this->denyAccessUnlessGranted(FlashcardVoter::OWNER, $flashcard, 'You can not update this resource');

        try {
            // Retrieve the request body
            $body = $requestPayloadService->getRequestPayload($request);

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
                ->resolve($body);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
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
    public function getFlashcardsByUnit(int $id, Request $request, FlashcardRepository $flashcardRepository): JsonResponse
    {
        $unit = $this->getResourceById(Unit::class, $id);

        $this->denyAccessUnlessGranted(UnitVoter::OWNER, $unit, 'You can not access this resource');

        $pagination = $this->getPaginationParameter(Flashcard::class, $request);

        // Get data with pagination
        $flashcards = $flashcardRepository->findByUnitWithPagination($pagination, $unit);

        return $this->jsonStd($flashcards, context: ['groups' => ['read:flashcard:user']]);
    }

    #[Route('/flashcards/{id}/review', name: 'review_flashcard', methods: ['PATCH'], requirements: ['id' => Regex::INTEGER])]
    public function reviewFlashcard(
        int $id,
        EntityManagerInterface $em,
        RequestPayloadService $requestPayloadService,
        Request $request,
        SpacedRepetitionOptionsResolver $spacedRepetitionOptionsResolver,
        SpacedRepetitionScheduler $spacedRepetitionScheduler,
        ReviewManager $reviewManager
    ): JsonResponse {
        $flashcard = $this->getResourceById(Flashcard::class, $id);
        $this->denyAccessUnlessGranted(FlashcardVoter::OWNER, $flashcard, 'You can not update this resource');

        // If the next review is in the future
        if ($flashcard->getNextReview() > (new \DateTimeImmutable())) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, 'You can not review the flashcard with id %d yet. The next review is scheduled for %s', [$flashcard->getId(), $flashcard->getNextReview()->format('jS \\of F Y')]);
        }

        try {
            $body = $requestPayloadService->getRequestPayload($request);

            $data = $spacedRepetitionOptionsResolver
                ->configureGrade()
                ->resolve($body);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        $spacedRepetitionScheduler->review($flashcard, $data['grade']);
        $this->validateEntity($flashcard);

        /** @var User $user */
        $user = $this->getUser();

        $review = $reviewManager->createReview($flashcard, $user, $data['grade']);
        $this->validateEntity($review);
        $em->persist($review);

        $em->flush();

        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/flashcards/reset', name: 'reset_all_flashcard', methods: ['PATCH'])]
    public function resetAllFlashcards(
        ReviewManager $reviewManager
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();
        $reviewManager->resetAllFlashcards($user);

        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/flashcards/{id}/reset', name: 'reset_flashcard', methods: ['PATCH'], requirements: ['id' => Regex::INTEGER])]
    public function resetFlashcard(
        int $id,
        ReviewManager $reviewManager
    ): JsonResponse {
        $flashcard = $this->getResourceById(Flashcard::class, $id);
        $this->denyAccessUnlessGranted(FlashcardVoter::OWNER, $flashcard, 'You can not update this resource');

        /** @var User $user */
        $user = $this->getUser();
        $reviewManager->resetFlashcard($flashcard, $user);

        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/flashcards/session', name: 'session_flashcard', methods: ['GET'])]
    public function getFlashcardSession(
        FlashcardRepository $flashcardRepository
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        $cardsToReview = $flashcardRepository->findFlashcardToReview($user, SpacedRepetitionScheduler::SESSION_SIZE);
        shuffle($cardsToReview);

        return $this->jsonStd($cardsToReview, context: ['groups' => ['read:flashcard:user']]);
    }

    #[Route('/flashcards/count', name: 'flashcard_count', methods: ['GET'])]
    public function countFlashcards(
        FlashcardRepository $flashcardRepository
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        $count = $flashcardRepository->countAll($user);

        return $this->jsonStd($count);
    }

    #[Route('/flashcards/reviews/count', name: 'flashcard_reviews_count', methods: ['GET'])]
    public function countFlashcardsToReview(
        FlashcardRepository $flashcardRepository
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        $count = $flashcardRepository->countFlashcardsToReview($user);

        return $this->jsonStd($count);
    }
}
