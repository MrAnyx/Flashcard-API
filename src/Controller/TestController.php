<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\RelativeToEntity;
use App\Entity\Topic;
use App\Entity\Unit;
use App\Modifier\Modifier;
use App\Modifier\Transformer\EntityByIdTransformer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/_internal', name: 'api_', format: 'json')]
#[RelativeToEntity(Topic::class)]
class TestController extends AbstractRestController
{
    #[Route('/test', name: 'test')]
    public function index(): JsonResponse
    {
        $entity = $this->decodeBody(
            classname: Unit::class,
            strict: false,
            deserializationGroups: ['write:unit:user'],
            transformers: [
                new Modifier('topic', EntityByIdTransformer::class, [
                    'entity' => Topic::class,
                ]),
            ],
            validationGroups: null
        );

        dd($entity);

        return $this->json($entity, context: ['groups' => ['read:user:user']]);
    }
}
