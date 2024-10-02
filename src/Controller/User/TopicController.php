<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use App\Entity\Session;
use App\Entity\Topic;
use App\Entity\User;
use App\Enum\SettingName;
use App\Exception\ApiException;
use App\OptionsResolver\TopicOptionsResolver;
use App\Repository\FlashcardRepository;
use App\Repository\ReviewRepository;
use App\Repository\TopicRepository;
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
        $topics = $topicRepository->findAllWithPagination($pagination, $user);

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
    ): JsonResponse {
        // Retrieve the request body
        $body = $this->getRequestPayload($request);

        try {
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
        Request $request,
        TopicOptionsResolver $flashcardOptionsResolver,
    ): JsonResponse {
        $topic = $this->getResourceById(Topic::class, $id);

        $this->denyAccessUnlessGranted(TopicVoter::OWNER, $topic, 'You can not update this resource');

        // Retrieve the request body
        $body = $this->getRequestPayload($request);

        try {
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

    #[Route('/topics/{id}/reset', name: 'reset_topic', methods: ['PATCH'], requirements: ['id' => Regex::INTEGER])]
    public function resetTopic(
        int $id,
        ReviewRepository $reviewRepository,
        FlashcardRepository $flashcardRepository,
    ): JsonResponse {
        $topic = $this->getResourceById(Topic::class, $id);
        $this->denyAccessUnlessGranted(TopicVoter::OWNER, $topic, 'You can not update this resource');

        /** @var User $user */
        $user = $this->getUser();

        $reviewRepository->resetBy($user, $topic);
        $flashcardRepository->resetBy($user, $topic);

        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/topics/{id}/session', name: 'session_topic', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getFlashcardSession(
        int $id,
        FlashcardRepository $flashcardRepository,
        EntityManagerInterface $em,
    ): JsonResponse {
        $topic = $this->getResourceById(Topic::class, $id);
        $this->denyAccessUnlessGranted(TopicVoter::OWNER, $topic, 'You can not update this resource');

        /** @var User $user */
        $user = $this->getUser();

        $cardsToReview = $flashcardRepository->findFlashcardToReviewBy($topic, $user, $this->getUserSetting(SettingName::FLASHCARD_PER_SESSION));

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

    #[Route('/topics/recent', name: 'recent_topic', methods: ['GET'])]
    public function getRecentTopics(TopicRepository $topicRepository): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $recentTopics = $topicRepository->findRecentTopics($user, 5);

        return $this->jsonStd($recentTopics, context: ['groups' => ['read:topic:user']]);
    }

    #[Route('/topics/count', name: 'topic_count', methods: ['GET'])]
    public function countTopics(
        TopicRepository $topicRepository,
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        $count = $topicRepository->countAll($user);

        return $this->jsonStd($count);
    }
}
