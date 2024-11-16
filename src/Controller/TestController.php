<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\RelativeToEntity;
use App\Entity\Topic;
use App\Entity\User;
use App\Model\Filter;
use App\Model\Page;
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
        Page $page,
        ?Filter $filter,
        // RequestDecoder $requestDecoder,
        // EntityManagerInterface $em,
    ): JsonResponse {
        // $existingEntity = $em->find(Topic::class, 1);

        // $entity = $requestDecoder->decode(
        //     classname: Topic::class,
        //     fromObject: $existingEntity,
        //     strict: true,
        //     deserializationGroups: ['write:topic:user']
        // );

        dd($page, $filter);

        return $this->jsonStd($page);
    }
}
