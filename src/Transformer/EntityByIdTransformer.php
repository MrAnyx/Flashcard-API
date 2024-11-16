<?php

declare(strict_types=1);

namespace App\Transformer;

use Doctrine\ORM\EntityManagerInterface;

class EntityByIdTransformer implements TransformerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
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

        return $entity;
    }
}
