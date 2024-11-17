<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\RelativeToEntity;
use App\Entity\Topic;
use App\Entity\User;
use App\Model\Filter;
use App\Model\Page;
use App\Repository\TopicRepository;
use App\Service\RequestDecoder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/_internal', name: 'api_', format: 'json')]
#[RelativeToEntity(Topic::class)]
class TestController extends AbstractRestController
{
    #[Route('/test', name: 'test')]
    public function index(
        // RequestDecoder $requestDecoder,
        // EntityManagerInterface $em,
        TopicRepository $topicRepository,
        Page $page,
        ?Filter $filter,
    ): JsonResponse {
        // $existingEntity = $em->find(Topic::class, 1);

        // $entity = $requestDecoder->decode(
        //     classname: Topic::class,
        //     fromObject: $existingEntity,
        //     strict: true,
        //     deserializationGroups: ['write:topic:user']
        // );

        $topics = $topicRepository->paginateAndFilterAll($page, $filter);

        return $this->json($topics, context: ['groups' => ['read:topic:user', 'read:pagination']]);
    }
}
