<?php

declare(strict_types=1);

namespace App\Modifier\Transformer;

use App\Exception\UnauthorizedException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class EntityByIdTransformer implements TransformerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
    ) {
    }

    public function transform(mixed $rawValue, array $context): mixed
    {
        if (!\array_key_exists('entity', $context)) {
            throw new \InvalidArgumentException("Missing option 'entity' in context");
        }

        try {
            $entity = $this->em->find($context['entity'], $rawValue);
        } catch (\Exception) {
            throw new \InvalidArgumentException(\sprintf('Can not find entity %s in database with id: %s', $context['entity'], $rawValue));
        }

        if (\array_key_exists('voter', $context)) {
            if (!$this->security->isGranted($context['voter'], $entity)) {
                throw new UnauthorizedException('You cannot access this resource');
            }
        }

        return $entity;
    }
}
