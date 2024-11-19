<?php

declare(strict_types=1);

namespace App\Modifier\Mutator;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class HashPasswordMutator implements MutatorInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function mutate(object &$entity, #[\SensitiveParameter] mixed $rawValue, array $context): void
    {
        if (!\is_string($rawValue)) {
            return;
        }

        if (!is_subclass_of($entity, PasswordAuthenticatedUserInterface::class)) {
            throw new \InvalidArgumentException(\sprintf('Entity %s must implement %s', $entity::class, PasswordAuthenticatedUserInterface::class));
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $passwordField = $context['passwordField'] ?? 'password';

        if (!$propertyAccessor->isWritable($entity, $passwordField)) {
            throw new \InvalidArgumentException(\sprintf('Property "%s" is not writable in entity %s', $passwordField, $entity::class));
        }
        $passwordHash = $this->passwordHasher->hashPassword($entity, $rawValue);
        $propertyAccessor->setValue($entity, $passwordField, $passwordHash);
    }
}
