<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Attribute\RelativeToEntity;
use App\Attribute\Resource;
use App\Controller\AbstractRestController;
use App\DTO\TopicDTO;
use App\Entity\Session;
use App\Entity\Topic;
use App\Entity\User;
use App\Enum\CountCriteria\TopicCountCriteria;
use App\Enum\SettingName;
use App\Model\Filter;
use App\Model\Page;
use App\Repository\FlashcardRepository;
use App\Repository\ReviewRepository;
use App\Repository\TopicRepository;
use App\Utility\Regex;
use App\Voter\TopicVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
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
        #[CurrentUser] User $user,
        #[MapRequestPayload('json')] TopicDTO $topicBodyPayload,
    ): JsonResponse {
        $this->validateEntity($topicBodyPayload, ['post']);

        $topic = new Topic();
        $topic
            ->setName($topicBodyPayload->name)
            ->setAuthor($user)
            ->setDescription($topicBodyPayload->description)
            ->setFavorite($topicBodyPayload->favorite);

        $this->validateEntity($topic);

        $em->persist($topic);
        $em->flush();

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
        #[Resource(TopicVoter::OWNER)] Topic $topic,
        #[MapRequestPayload('json')] TopicDTO $topicBodyPayload,
    ): JsonResponse {
        $validationGroups = $request->getMethod() === 'PUT' ? ['put'] : ['patch'];

        $this->validateEntity($topicBodyPayload, $validationGroups);

        if (isset($topicBodyPayload->name)) {
            $topic->setName($topicBodyPayload->name);
        }
        if (isset($topicBodyPayload->description)) {
            $topic->setDescription($topicBodyPayload->description);
        }
        if (isset($topicBodyPayload->favorite)) {
            $topic->setFavorite($topicBodyPayload->favorite);
        }

        $this->validateEntity($topic);

        $em->flush();

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
