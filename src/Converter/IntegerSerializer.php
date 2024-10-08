<?php

declare(strict_types=1);

namespace App\Converter;

class IntegerSerializer implements SerializerInterface
{
    public function serialize(mixed $value): string
    {
        return (string) $value;
    }

    public function deserialize(string $value): int
    {
        if (filter_var($value, \FILTER_VALIDATE_INT) === false) {
            throw new \InvalidArgumentException(\sprintf('The filter value "%s" is not a valid integer.', $value));
        }

        return (int) $value;
    }
}
