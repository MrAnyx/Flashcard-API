<?php

declare(strict_types=1);

namespace App\Filter;

use App\Attribute\Searchable;
use App\Filter\Converter\BooleanConverter;
use App\Filter\Converter\FilterValueConverterInterface;
use App\Filter\Converter\FloatConverter;
use App\Filter\Converter\IntegerConverter;
use App\Filter\Converter\StringConverter;
use App\Service\AttributeParser;
use Doctrine\ORM\EntityManagerInterface;

class FilterConverter
{
    public function __construct(
        private readonly AttributeParser $attributeParser,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function convert(string $entityFqcn, string $propertyName, string $value): mixed
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
            $converter = $this->instantiateConverter($searchableAttribute->converterFqcn, $searchableAttribute->options);
        }

        return $converter->convert($value);
    }

    private function getDefaultConverter(\ReflectionNamedType $reflectionType): FilterValueConverterInterface
    {
        $type = $reflectionType->getName();

        return match ($type) {
            'int', 'integer' => new IntegerConverter(),
            'float' => new FloatConverter(),
            'bool', 'boolean' => new BooleanConverter(),
            'string' => new StringConverter(),
            default => throw new \RuntimeException("No default converter available for type {$type}"),
        };
    }

    private function instantiateConverter(string $converterClass, array $options): FilterValueConverterInterface
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
