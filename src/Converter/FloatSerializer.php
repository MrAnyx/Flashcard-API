<?php

declare(strict_types=1);

namespace App\Converter;

class FloatSerializer implements SerializerInterface
{
    public function serialize(mixed $value): string
    {
        return (string) $value;
    }

    public function deserialize(string $value): float
    {
        if (filter_var($value, \FILTER_VALIDATE_FLOAT) === false) {
            throw new \InvalidArgumentException(\sprintf('The filter value "%s" is not a valid float.', $value));
        }

        return (float) $value;
    }
}
