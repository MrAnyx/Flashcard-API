<?php

declare(strict_types=1);

namespace App\Modifier\Transformer;

use App\Exception\UnauthorizedException;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class EntityByTransformer implements TransformerInterface
{
    public function __construct(
        protected readonly EntityManagerInterface $em,
        protected readonly Security $security,
    ) {
    }

    public function transform(mixed $rawValue, array $context): mixed
    {
        if (!\array_key_exists('entity', $context)) {
            throw new \InvalidArgumentException("Missing option 'entity' in context");
        }

        if (!\array_key_exists('property', $context)) {
            throw new \InvalidArgumentException("Missing option 'property' in context");
        }

        $entity = $this->findEntityBy($context['entity'], $context['property'], $rawValue);

        if (\array_key_exists('voter', $context)) {
            if (!$this->security->isGranted($context['voter'], $entity)) {
                throw new UnauthorizedException('You cannot access this resource');
            }
        }

        return $entity;
    }

    protected function findEntityBy(string $classname, string $property, mixed $value): object
    {
        try {
            $repository = $this->em->getRepository($classname);
            $entity = $repository->findOneBy([$property => $value], [$property => Order::Ascending->value]);
        } catch (\Exception $ex) {
            throw new \InvalidArgumentException(\sprintf('Can not find entity %s in database with %s: %s', $classname, $property, $value), previous: $ex);
        }

        if ($entity === null) {
            throw new \InvalidArgumentException(\sprintf('Can not find entity %s in database with %s: %s', $classname, $property, $value));
        }

        return $entity;
    }
}
