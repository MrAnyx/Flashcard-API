<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Attribute\Resource;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Service\ResourceFinder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResourceByIdResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly ResourceFinder $resourceFinder,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $resourceAttribute = $argument->getAttributes(Resource::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;

        if (!($resourceAttribute instanceof Resource)) {
            return [];
        }

        $entityClass = $argument->getType();
        $id = $request->attributes->get($resourceAttribute->idUrlSegment);

        try {
            $resource = $this->resourceFinder->getResourceById($entityClass, $id, false, $resourceAttribute->voterAttribute);

            return [
                $resource,
            ];
        } catch (NotFoundException $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        } catch (UnauthorizedException $ex) {
            throw new AccessDeniedHttpException($ex->getMessage());
        }
    }
}
