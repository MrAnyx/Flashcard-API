<?php

namespace App\Service;

use App\Attribut\Sortable;
use InvalidArgumentException;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityChecker
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    public function entityExists(string $classname): bool
    {
        return class_exists($classname);
    }

    public function fieldExists(string $classname, string $field): bool
    {
        return property_exists($classname, $field);
    }

    /**
     * @throws InvalidArgumentException if $classname corresponds to an unknown class
     * @throws InvalidArgumentException if $field correspond to an unknown property of $classname
     */
    public function isFieldSortable(string $classname, string $field): bool
    {
        if (! $this->entityExists($classname)) {
            throw new InvalidArgumentException("Unknown class $classname");
        }

        if (! $this->fieldExists($classname, $field)) {
            throw new InvalidArgumentException("Field $field doesn't exist in entity $classname");
        }

        $reflectionClass = new \ReflectionClass($classname);

        $sortableAttributes = $reflectionClass
            ->getProperty($field)
            ->getAttributes(Sortable::class);

        return count($sortableAttributes) > 0;
    }

    /**
     * @throws InvalidArgumentException if $classname corresponds to an unknown class
     */
    public function getSortableFields(string $classname): array
    {
        if (! $this->entityExists($classname)) {
            throw new InvalidArgumentException("Unknown class $classname");
        }

        $sortableFields = [];

        $reflectionClass = new \ReflectionClass($classname);
        $reflectionProperties = $reflectionClass->getProperties();

        /** @var \ReflectionProperty $property */
        foreach ($reflectionProperties as $property) {
            if (count($property->getAttributes(Sortable::class)) > 0) {
                $sortableFields[] = $property->getName();
            }
        }

        return $sortableFields;
    }

    public function validateEntity(mixed $entity, array $validationGroups = ['Default']): void
    {
        $errors = $this->validator->validate($entity, groups: $validationGroups);
        if (count($errors) > 0) {
            throw new ValidatorException((string) $errors[0]->getMessage());
        }
    }
}
