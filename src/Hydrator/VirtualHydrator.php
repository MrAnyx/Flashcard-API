<?php

declare(strict_types=1);

namespace App\Hydrator;

use App\Attribut\Virtual;
use Doctrine\ORM\Internal\Hydration\ObjectHydrator;

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

                // Hydrate only the fields that are mapped by Virtual attributes
                foreach ($propertyMappings as [$propertyName, $fieldName]) {
                    $setterMethod = 'set' . ucfirst($propertyName->name);

                    if (method_exists($object, $setterMethod) && isset($row[$fieldName])) {
                        // Dynamically call the setter method
                        \call_user_func([$object, $setterMethod], $row[$fieldName]);

                        // Remove the hydrated key from the data array
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
