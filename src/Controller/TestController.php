<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\RelativeToEntity;
use App\Entity\Topic;
use App\Entity\User;
use App\Modifier\Modifier;
use App\Modifier\Mutator\HashPasswordMutator;
use App\Service\RequestDecoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/_internal', name: 'api_', format: 'json')]
#[RelativeToEntity(Topic::class)]
class TestController extends AbstractRestController
{
    #[Route('/test', name: 'test')]
    public function index(
        RequestDecoder $requestDecoder,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $entity = $requestDecoder->decode(
            classname: User::class,
            fromObject: $user,
            deserializationGroups: ['write:user:user'],
            mutators: [
                new Modifier('rawPassword', HashPasswordMutator::class),
            ],
        );

        if ($entity->getRawPassword() !== null) {
            $this->validateEntity($entity, ['Default', 'edit:user:password']);
        }

        return $this->json($entity, context: ['groups' => ['read:user:user']]);
    }
}
