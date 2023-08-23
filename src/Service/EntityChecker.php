<?php

namespace App\Service;

use Exception;
use App\Attribut\Sortable;
use InvalidArgumentException;

class EntityChecker
{
    public function entityExists(string $classname)
    {
        return class_exists($classname);
    }

    public function fieldExists(string $classname, string $field)
    {
        return property_exists($classname, $field);
    }

    /**
     * @throws Exception if $classname corresponds to an unknown class
     * @throws InvalidArgumentException if $field correspond to an unknown property of $classname
     */
    public function isFieldSortable(string $classname, string $field)
    {
        if (! $this->entityExists($classname)) {
            throw new Exception("Unknown class $classname");
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
     * @throws Exception if $classname corresponds to an unknown class
     */
    public function getSortableFields(string $classname)
    {
        if (! $this->entityExists($classname)) {
            throw new Exception("Unknown class $classname");
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
