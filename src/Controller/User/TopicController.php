<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Attribute\Body;
use App\Attribute\RelativeToEntity;
use App\Attribute\Resource;
use App\Controller\AbstractRestController;
use App\Entity\Session;
use App\Entity\Topic;
use App\Entity\User;
use App\Enum\CountCriteria\TopicCountCriteria;
use App\Enum\SettingName;
use App\Exception\ApiException;
use App\Model\Filter;
use App\Model\Page;
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
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
class TopicController extends AbstractRestController
{
    #[Route('/topics', name: 'get_topics', methods: ['GET'])]
    public function getTopics(
        TopicRepository $topicRepository,
        #[RelativeToEntity(Topic::class)] Page $page,
        #[RelativeToEntity(Topic::class)] Filter $filter,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $topics = $topicRepository->paginateAndFilterAll($page, $filter, $user);

        return $this->jsonStd($topics, context: ['groups' => ['read:topic:user']]);
    }

    #[Route('/topics/{id}', name: 'get_topic', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getTopic(
        #[Resource(TopicVoter::OWNER)] Topic $topic,
    ): JsonResponse {
        return $this->jsonStd($topic, context: ['groups' => ['read:topic:user']]);
    }

    #[Route('/topics', name: 'create_topic', methods: ['POST'])]
    public function createTopic(
        EntityManagerInterface $em,
        TopicOptionsResolver $topicOptionsResolver,
        #[CurrentUser] User $user,
        #[Body] mixed $body,
    ): JsonResponse {
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
    public function deleteTopic(
        EntityManagerInterface $em,
        #[Resource(TopicVoter::OWNER)] Topic $topic,
    ): JsonResponse {
        $em->remove($topic);
        $em->flush();

        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/topics/{id}', name: 'update_topic', methods: ['PATCH', 'PUT'], requirements: ['id' => Regex::INTEGER])]
    public function updateTopic(
        EntityManagerInterface $em,
        Request $request,
        TopicOptionsResolver $flashcardOptionsResolver,
        #[Resource(TopicVoter::OWNER)] Topic $topic,
        #[Body] mixed $body,
    ): JsonResponse {
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
        ReviewRepository $reviewRepository,
        FlashcardRepository $flashcardRepository,
        #[CurrentUser] User $user,
        #[Resource(TopicVoter::OWNER)] Topic $topic,
    ): JsonResponse {
        $reviewRepository->resetBy($user, $topic);
        $flashcardRepository->resetBy($user, $topic);

        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/topics/{id}/session', name: 'session_topic', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getFlashcardSession(
        FlashcardRepository $flashcardRepository,
        EntityManagerInterface $em,
        #[CurrentUser] User $user,
        #[Resource(TopicVoter::OWNER)] Topic $topic,
    ): JsonResponse {
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
    public function getRecentTopics(
        TopicRepository $topicRepository,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $recentTopics = $topicRepository->findRecentTopics($user, 5);

        return $this->jsonStd($recentTopics, context: ['groups' => ['read:topic:user']]);
    }

    #[Route('/topics/count', name: 'topic_count', methods: ['GET'])]
    public function countTopics(
        TopicRepository $topicRepository,
        Request $request,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $criteria = $this->getCountCriteria($request, TopicCountCriteria::class, TopicCountCriteria::ALL->value);

        $count = match ($criteria) {
            TopicCountCriteria::ALL => $topicRepository->countAll($user),
        };

        return $this->jsonStd($count);
    }
}
