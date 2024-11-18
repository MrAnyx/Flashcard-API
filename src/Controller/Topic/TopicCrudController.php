<?php

declare(strict_types=1);

namespace App\Controller\Topic;

use App\Attribute\RelativeToEntity;
use App\Attribute\Resource;
use App\Controller\AbstractRestController;
use App\Entity\Topic;
use App\Entity\User;
use App\Model\Filter;
use App\Model\Page;
use App\Repository\TopicRepository;
use App\Utility\Regex;
use App\Voter\TopicVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
#[RelativeToEntity(Topic::class)]
class TopicCrudController extends AbstractRestController
{
    #[Route('/topics', name: 'get_topics', methods: ['GET'])]
    public function getTopics(
        TopicRepository $topicRepository,
        Page $page,
        ?Filter $filter,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $topics = $topicRepository->paginateAndFilterAll($page, $filter, $user);

        return $this->json($topics, context: ['groups' => ['read:topic:user', 'read:pagination']]);
    }

    #[Route('/topics/{id}', name: 'get_topic', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getTopic(
        #[Resource(TopicVoter::OWNER)] Topic $topic,
    ): JsonResponse {
        return $this->json($topic, context: ['groups' => ['read:topic:user']]);
    }

    #[Route('/topics', name: 'create_topic', methods: ['POST'])]
    public function createTopic(
        EntityManagerInterface $em,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $topic = $this->decodeBody(
            classname: Topic::class,
            deserializationGroups: ['write:topic:user'],
            validationGroups: null
        );

        $topic->setAuthor($user);

        $this->validateEntity($topic);

        $em->persist($topic);
        $em->flush();

        return $this->json(
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

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/topics/{id}', name: 'update_topic', methods: ['PATCH', 'PUT'], requirements: ['id' => Regex::INTEGER])]
    public function updateTopic(
        EntityManagerInterface $em,
        #[Resource(TopicVoter::OWNER)] Topic $topic,
    ): JsonResponse {
        $updatedTopic = $this->decodeBody(
            classname: Topic::class,
            fromObject: $topic,
            deserializationGroups: ['write:topic:user'],
        );

        $em->flush();

        return $this->json($updatedTopic, context: ['groups' => ['read:topic:user']]);
    }

    #[Route('/topics/recent', name: 'recent_topic', methods: ['GET'])]
    public function getRecentTopics(
        TopicRepository $topicRepository,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $recentTopics = $topicRepository->findRecentTopics($user, 5);

        return $this->json($recentTopics, context: ['groups' => ['read:topic:user']]);
    }
}
