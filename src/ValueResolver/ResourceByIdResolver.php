<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Attribute\Resource;
use App\Exception\ApiException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ResourceByIdResolver implements ValueResolverInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $entityClass = $argument->getType();
        $id = $request->attributes->get('id');

        $resource = $this->entityManager->find($entityClass, $id);

        if ($resource === null) {
            throw new ApiException(Response::HTTP_NOT_FOUND, 'Resource of type %s with id %d was not found', [$entityClass, $id]);
        }

        $resourceAttribute = $argument->getAttributes(Resource::class)[0] ?? null;
        $voterAttribute = $resourceAttribute?->voterAttribute ?? null;

        if ($voterAttribute && !$this->security->isGranted($resourceAttribute->voterAttribute, $resource)) {
            throw new AccessDeniedHttpException('You cannot access this resource');
        }

        return [
            $resource,
        ];
    }
}
