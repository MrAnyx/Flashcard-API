<?php

declare(strict_types=1);

namespace App\Modifier\Transformer;

use App\Exception\UnauthorizedException;

class EntityByIdTransformer extends EntityByTransformer
{
    public function transform(mixed $rawValue, array $context): mixed
    {
        if (!\array_key_exists('entity', $context)) {
            throw new \InvalidArgumentException("Missing option 'entity' in context");
        }

        $entity = $this->findEntityBy($context['entity'], 'id', $rawValue);

        if (\array_key_exists('voter', $context)) {
            if (!$this->security->isGranted($context['voter'], $entity)) {
                throw new UnauthorizedException('You cannot access this resource');
            }
        }

        return $entity;
    }
}
