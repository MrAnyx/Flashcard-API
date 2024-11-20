<?php

declare(strict_types=1);

namespace App\Controller\Topic;

use App\Attribute\RelativeToEntity;
use App\Controller\AbstractRestController;
use App\Entity\Topic;
use App\Entity\User;
use App\Enum\CountCriteria\TopicCountCriteria;
use App\Repository\TopicRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
#[RelativeToEntity(Topic::class)]
class TopicScalarController extends AbstractRestController
{
    #[Route('/topics/count/{criteria}', name: 'count_topics', methods: ['GET'])]
    public function countTopics(
        TopicRepository $topicRepository,
        #[CurrentUser] User $user,
        TopicCountCriteria $criteria = TopicCountCriteria::ALL,
    ): JsonResponse {
        $count = match ($criteria) {
            TopicCountCriteria::ALL => $topicRepository->countAll($user),
        };

        return $this->json($count);
    }
}
