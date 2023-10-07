<?php

namespace App\Service;

use App\Attribut\Sortable;
use InvalidArgumentException;

class SortableEntityChecker
{
    public function isFieldSortable(string $classname, string $field): bool
    {
        if (! class_exists($classname)) {
            throw new InvalidArgumentException("Unknown class $classname");
        }

        if (! property_exists($classname, $field)) {
            throw new InvalidArgumentException("Field $field doesn't exist in entity $classname");
        }

        $reflectionClass = new \ReflectionClass($classname);

        $sortableAttributes = $reflectionClass
            ->getProperty($field)
            ->getAttributes(Sortable::class);

        return count($sortableAttributes) > 0;
    }

    public function getSortableFields(string $classname): array
    {
        if (! class_exists($classname)) {
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
}
