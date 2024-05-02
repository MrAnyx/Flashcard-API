<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use App\Entity\Topic;
use App\Entity\Unit;
use App\Entity\User;
use App\Exception\ApiException;
use App\OptionsResolver\TopicOptionsResolver;
use App\Repository\FlashcardRepository;
use App\Repository\TopicRepository;
use App\Repository\UnitRepository;
use App\Service\RequestPayloadService;
use App\Service\ReviewManager;
use App\Service\SpacedRepetitionScheduler;
use App\Utility\Regex;
use App\Voter\TopicVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', 'api_', format: 'json')]
class TopicController extends AbstractRestController
{
    #[Route('/topics', name: 'get_topics', methods: ['GET'])]
    public function getAllTopics(Request $request, TopicRepository $topicRepository): JsonResponse
    {
        $pagination = $this->getPaginationParameter(Topic::class, $request);

        /** @var User $user */
        $user = $this->getUser();

        // Get data with pagination
        $topics = $topicRepository->findAllWithPagination(
            $pagination->page,
            $pagination->sort,
            $pagination->order,
            $user
        );

        // Return paginate data
        return $this->jsonStd($topics, context: ['groups' => ['read:topic:user']]);
    }

    #[Route('/topics/{id}', name: 'get_topic', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getOneTopic(int $id): JsonResponse
    {
        $topic = $this->getResourceById(Topic::class, $id);

        $this->denyAccessUnlessGranted(TopicVoter::OWNER, $topic, 'You can not access this resource');

        return $this->jsonStd($topic, context: ['groups' => ['read:topic:user']]);
    }

    #[Route('/topics', name: 'create_topic', methods: ['POST'])]
    public function createTopic(
        Request $request,
        EntityManagerInterface $em,
        TopicOptionsResolver $topicOptionsResolver,
        RequestPayloadService $requestPayloadService
    ): JsonResponse {
        try {
            // Retrieve the request body
            $body = $requestPayloadService->getRequestPayload($request);

            // Validate the content of the request body
            $data = $topicOptionsResolver
                ->configureName(true)
                ->configureDescription(true)
                ->configureFavorite(true)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        /** @var User $user */
        $user = $this->getUser();

        // Temporarly create the element
        $topic = new Topic();
        $topic
            ->setName($data['name'])
            ->setAuthor($user)
            ->setDescription($data['description'])
            ->setFavorite($data['favorite']);

        // Second validation using the validation constraints
        $this->validateEntity($topic);

        // Save the new element
        $em->persist($topic);
        $em->flush();

        // Return the element with the the status 201 (Created)
        return $this->jsonStd(
            $topic,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('api_get_topic', ['id' => $topic->getId()])],
            ['groups' => ['read:topic:user']]
        );
    }

    #[Route('/topics/{id}', name: 'delete_topic', methods: ['DELETE'], requirements: ['id' => Regex::INTEGER])]
    public function deleteTopic(int $id, EntityManagerInterface $em): JsonResponse
    {
        $topic = $this->getResourceById(Topic::class, $id);

        $this->denyAccessUnlessGranted(TopicVoter::OWNER, $topic, 'You can not delete this resource');

        // Remove the element
        $em->remove($topic);
        $em->flush();

        // Return a response with status 204 (No Content)
        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/topics/{id}', name: 'update_topic', methods: ['PATCH', 'PUT'], requirements: ['id' => Regex::INTEGER])]
    public function updateTopic(
        int $id,
        EntityManagerInterface $em,
        RequestPayloadService $requestPayloadService,
        Request $request,
        TopicOptionsResolver $flashcardOptionsResolver,
    ): JsonResponse {
        $topic = $this->getResourceById(Topic::class, $id);

        $this->denyAccessUnlessGranted(TopicVoter::OWNER, $topic, 'You can not update this resource');

        try {
            // Retrieve the request body
            $body = $requestPayloadService->getRequestPayload($request);

            // Check if the request method is PUT. In this case, all parameters must be provided in the request body.
            // Otherwise, all parameters are optional.
            $mandatoryParameters = $request->getMethod() === 'PUT';

            // Validate the content of the request body
            $data = $flashcardOptionsResolver
                ->configureName($mandatoryParameters)
                ->configureDescription($mandatoryParameters)
                ->configureFavorite($mandatoryParameters)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        // Update each fields if necessary
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'name':
                    $topic->setName($value);
                    break;
                case 'description':
                    $topic->setDescription($value);
                    break;
                case 'favorite':
                    $topic->setFavorite($value);
                    break;
            }
        }

        // Second validation using the validation constraints
        $this->validateEntity($topic);

        // Save the element information
        $em->flush();

        // Return the element
        return $this->jsonStd($topic, context: ['groups' => ['read:topic:user']]);
    }

    #[Route('/topics/{id}/units', name: 'get_units_by_topic', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getUnitsFromTopic(int $id, Request $request, UnitRepository $unitRepository): JsonResponse
    {
        $topic = $this->getResourceById(Topic::class, $id);

        $this->denyAccessUnlessGranted(TopicVoter::OWNER, $topic, 'You can not access this resource');

        $pagination = $this->getPaginationParameter(Unit::class, $request);

        // Get data with pagination
        $units = $unitRepository->findByTopicWithPagination(
            $pagination->page,
            $pagination->sort,
            $pagination->order,
            $topic
        );

        return $this->jsonStd($units, context: ['groups' => ['read:unit:user']]);
    }

    #[Route('/topics/{id}/flashcards/reset', name: 'reset_topic', methods: ['PATCH'], requirements: ['id' => Regex::INTEGER])]
    public function resetUnit(
        int $id,
        ReviewManager $reviewManager
    ): JsonResponse {
        $topic = $this->getResourceById(Topic::class, $id);
        $this->denyAccessUnlessGranted(TopicVoter::OWNER, $topic, 'You can not update this resource');

        /** @var User $user */
        $user = $this->getUser();
        $reviewManager->resetFlashcards($topic, $user);

        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/topics/{id}/session', name: 'session_topic', methods: ['GET'])]
    public function getFlashcardSession(
        int $id,
        FlashcardRepository $flashcardRepository
    ): JsonResponse {
        $topic = $this->getResourceById(Topic::class, $id);
        $this->denyAccessUnlessGranted(TopicVoter::OWNER, $topic, 'You can not update this resource');

        /** @var User $user */
        $user = $this->getUser();

        $cardsToReview = $flashcardRepository->findFlashcardToReviewBy($topic, $user, SpacedRepetitionScheduler::SESSION_SIZE);
        shuffle($cardsToReview);

        return $this->jsonStd($cardsToReview, context: ['groups' => ['read:flashcard:user']]);
    }
}
