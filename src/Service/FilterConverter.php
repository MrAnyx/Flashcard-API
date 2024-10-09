<?php

declare(strict_types=1);

namespace App\Service;

use App\Attribute\Searchable;
use App\Enum\OperatorType;
use App\FilterConverter\BooleanConverter;
use App\FilterConverter\FilterConverterInterface;
use App\FilterConverter\FloatConverter;
use App\FilterConverter\IntegerConverter;
use App\FilterConverter\StringConverter;

class FilterConverter
{
    public function __construct(
        private readonly AttributeParser $attributeParser,
    ) {
    }

    public function convert(string $entityFqcn, string $propertyName, string $value, OperatorType $operator): mixed
    {
        if (!$this->attributeParser->hasAttribute($entityFqcn, $propertyName, Searchable::class)) {
            throw new \InvalidArgumentException(\sprintf('Property %s in entity %s is not searchable', $propertyName, $entityFqcn));
        }

        $reflectionProperty = new \ReflectionProperty($entityFqcn, $propertyName);
        $searchableAttributeReflection = $reflectionProperty->getAttributes(Searchable::class);

        if (\count($searchableAttributeReflection) > 1) {
            throw new \RuntimeException(\sprintf('Property %s on entity %s has %s attribute duplication', $propertyName, $entityFqcn, Searchable::class));
        }

        $searchableAttribute = $searchableAttributeReflection[0]->newInstance();

        if ($searchableAttribute->converterFqcn === null) {
            $reflectionType = $reflectionProperty->getType();

            if (!$reflectionType instanceof \ReflectionNamedType) {
                throw new \RuntimeException(\sprintf('Property %s on entity %s has invalid type. It must be a named type', $propertyName, $entityFqcn));
            }

            $converter = $this->getDefaultConverter($reflectionType);
        } else {
            $converter = $this->instantiateConverter($searchableAttribute->converterFqcn, $searchableAttribute->converterConstructorParams);
        }

        if (!\in_array($operator, $converter->getSupportedOperators())) {
            $supportedOperators = array_map(fn (OperatorType $type) => $type->value, $converter->getSupportedOperators());

            throw new \InvalidArgumentException(\sprintf('Operator %s is not supported here. Supported operator are %s', $operator->value, implode(', ', $supportedOperators)));
        }

        return $converter->deserialize($value);
    }

    private function getDefaultConverter(\ReflectionNamedType $reflectionType): FilterConverterInterface
    {
        $type = $reflectionType->getName();

        return match ($type) {
            'int', 'integer' => new IntegerConverter(),
            'float', 'double' => new FloatConverter(),
            'bool', 'boolean' => new BooleanConverter(),
            'string' => new StringConverter(),
            default => throw new \RuntimeException("No default converter available for type {$type}"),
        };
    }

    private function instantiateConverter(string $converterClass, array $options): FilterConverterInterface
    {
        if (!class_exists($converterClass)) {
            throw new \RuntimeException("Converter {$converterClass} does not exist");
        }

        $converter = new \ReflectionClass($converterClass);
        $constructor = $converter->getConstructor();

        if ($constructor) {
            $constructorParams = $constructor->getParameters();
            $constructorArgs = $this->mapOptionsToConstructorArgs($constructorParams, $options);
        } else {
            $constructorArgs = []; // No constructor parameters
        }

        return $converter->newInstanceArgs($constructorArgs);
    }

    /**
     * @param \ReflectionParameter[] $constructorParams
     */
    private function mapOptionsToConstructorArgs(array $constructorParams, array $options): array
    {
        $constructorArgs = [];

        foreach ($constructorParams as $param) {
            $paramName = $param->getName();

            if (\array_key_exists($paramName, $options)) {
                $constructorArgs[] = $options[$paramName];
            } elseif ($param->isOptional()) {
                $constructorArgs[] = $param->getDefaultValue();
            } else {
                throw new \RuntimeException("Missing required option for parameter '{$paramName}' in converter.");
            }
        }

        return $constructorArgs;
    }
}
