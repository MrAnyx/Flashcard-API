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
                    return json_decode($value, true);
                case 'object':
                    return json_decode($value);
                case 'callable':
                    // This is a complex case, often requires custom logic
                    throw new \InvalidArgumentException('Cannot convert string to callable type.');
                case 'iterable':
                    return json_decode($value, true);
                default:
                    if (class_exists($typeName)) {
                        return unserialize($value) ?: null;
                    }
                    throw new \InvalidArgumentException("Unsupported type: {$typeName}");
            }
        } elseif ($type instanceof \ReflectionUnionType) {
            foreach ($type->getTypes() as $subType) {
                try {
                    return self::convertStringToType($value, $subType);
                } catch (\InvalidArgumentException $e) {
                    // Continue to try next type
                }
            }
            throw new \InvalidArgumentException('None of the union types could handle the conversion.');
        } elseif ($type instanceof \ReflectionIntersectionType) {
            // Intersection types are more complex and often require custom logic for specific cases.
            throw new \InvalidArgumentException('Intersection types are not supported in this example.');
        }

        throw new \InvalidArgumentException('Unsupported ReflectionType provided.');
    }
}
