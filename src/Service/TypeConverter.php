<?php

declare(strict_types=1);

namespace App\Service;

class TypeConverter
{
    public static function convertStringToType(string $value, \ReflectionType $type)
    {
        if ($type instanceof \ReflectionNamedType) {
            $typeName = $type->getName();

            switch ($typeName) {
                case 'int':
                    return (int) $value;
                case 'float':
                    return (float) $value;
                case 'string':
                    return $value;
                case 'bool':
                    return filter_var($value, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE);
                case 'array':
                case 'iterable':
                case 'object':
                    return json_decode($value, true);
                default:
                    throw new \RuntimeException("Unsupported type: {$typeName}");
            }
        } elseif ($type instanceof \ReflectionUnionType) {
            foreach ($type->getTypes() as $subType) {
                try {
                    return self::convertStringToType($value, $subType);
                } catch (\InvalidArgumentException $e) {
                    // Continue to try next type
                }
            }
            throw new \RuntimeException('None of the union types could handle the conversion.');
        } elseif ($type instanceof \ReflectionIntersectionType) {
            // Intersection types are more complex and often require custom logic for specific cases.
            throw new \RuntimeException('Intersection types are not supported in this example.');
        }

        throw new \RuntimeException('Unsupported ReflectionType provided.');
    }
}
