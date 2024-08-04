<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\TypedField;

class AttributeHelper
{
    /**
     * @template T
     *
     * @param class-string<T> $fieldAttribute
     */
    public function hasAttribute(string $classname, string $field, string $fieldAttribute): bool
    {
        if (!class_exists($classname)) {
            throw new \InvalidArgumentException("Unknown class {$classname}");
        }

        if (!property_exists($classname, $field)) {
            throw new \InvalidArgumentException("Field {$field} doesn't exist in entity {$classname}");
        }

        $reflectionClass = new \ReflectionClass($classname);

        $fields = $reflectionClass
            ->getProperty($field)
            ->getAttributes($fieldAttribute);

        return \count($fields) > 0;
    }

    /**
     * @template T
     *
     * @param class-string<T> $fieldAttribute
     *
     * @return TypedFields[]
     */
    public function getFieldsWithAttribute(string $classname, string $fieldAttribute): array
    {
        if (!class_exists($classname)) {
            throw new \InvalidArgumentException("Unknown class {$classname}");
        }

        $fields = [];

        $reflectionClass = new \ReflectionClass($classname);
        $reflectionProperties = $reflectionClass->getProperties();

        /** @var \ReflectionProperty $property */
        foreach ($reflectionProperties as $property) {
            if (\count($property->getAttributes($fieldAttribute)) > 0) {
                $fields[] = new TypedField(
                    $property->getName(),
                    $property->getType()
                );
            }
        }

        return $fields;
    }
}
