<?php

declare(strict_types=1);

namespace App\Hydrator;

use App\Attribute\Virtual;
use Doctrine\ORM\Internal\Hydration\ObjectHydrator;
use Symfony\Component\PropertyAccess\PropertyAccess;

class VirtualHydrator extends ObjectHydrator
{
    /**
     * @var array<string, array<array{\ReflectionProperty, string}>>
     */
    private array $propertyCache = [];

    protected function hydrateAllData(): array
    {
        $data = parent::hydrateAllData();

        foreach ($data as $rowNumber => $row) {
            foreach ($row as $object) {
                if (!\is_object($object)) {
                    continue;
                }

                $className = $object::class;

                // Check if we already cached the properties with Virtual attributes for this class
                if (!isset($this->propertyCache[$className])) {
                    $this->propertyCache[$className] = $this->buildPropertyCache($className);
                }

                $propertyMappings = $this->propertyCache[$className];

                $propertyAccessor = PropertyAccess::createPropertyAccessor();

                // Hydrate only the fields that are mapped by Virtual attributes
                foreach ($propertyMappings as [$propertyName, $fieldName]) {
                    if ($propertyAccessor->isWritable($object, $propertyName->name) && isset($row[$fieldName])) {
                        $propertyAccessor->setValue($object, $propertyName->name, $row[$fieldName]);
                        unset($data[$rowNumber][$fieldName]);
                    }
                }
            }

            // Flatten array if it only contains 1 element or less
            if (\count($data[$rowNumber]) <= 1) {
                $data[$rowNumber] = $data[$rowNumber][array_key_first($data[$rowNumber])];
            }
        }

        return $data;
    }

    /**
     * Build and cache properties for a given class that have the Virtual attribute.
     */
    private function buildPropertyCache(string $className): array
    {
        $reflectionClass = new \ReflectionClass($className);
        $propertyMappings = [];

        foreach ($reflectionClass->getProperties() as $property) {
            $attributes = $property->getAttributes(Virtual::class);

            foreach ($attributes as $attribute) {
                /** @var Virtual $virtual */
                $virtual = $attribute->newInstance();
                $fieldName = $virtual->hydrateFrom;

                // Cache the property and the field name from the Virtual attribute
                $property->setAccessible(true);
                $propertyMappings[] = [$property, $fieldName];
            }
        }

        return $propertyMappings;
    }
}
