<?php

declare(strict_types=1);

namespace App\Service;

class AttributeParser
{
    /**
     * @return \ReflectionProperty[]
     */
    public function getFieldsWithAttribute(string $classnameFqcn, string $attributeFqcn): array
    {
        if (!class_exists($classnameFqcn)) {
            throw new \InvalidArgumentException("Unknown class {$classnameFqcn}");
        }

        if (!class_exists($attributeFqcn)) {
            throw new \InvalidArgumentException("Unknown attribute {$attributeFqcn}");
        }

        $fields = [];

        $reflectionClass = new \ReflectionClass($classnameFqcn);

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if (\count($reflectionProperty->getAttributes($attributeFqcn)) > 0) {
                $fields[] = $reflectionProperty;
            }
        }

        return $fields;
    }

    public function hasAttribute(string $classnameFqcn, string $propertyName, string $attributeFqcn): bool
    {
        if (!class_exists($classnameFqcn)) {
            throw new \InvalidArgumentException("Unknown class {$classnameFqcn}");
        }

        if (!property_exists($classnameFqcn, $propertyName)) {
            throw new \InvalidArgumentException("Field {$propertyName} doesn't exist in entity {$classnameFqcn}");
        }

        $reflectionProperty = new \ReflectionProperty($classnameFqcn, $propertyName);

        return \count($reflectionProperty->getAttributes($attributeFqcn)) > 0;
    }
}
