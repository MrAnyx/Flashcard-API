<?php

declare(strict_types=1);

namespace App\Serializer;

class BooleanSerializer implements SerializerInterface
{
    public function serialize(mixed $value): string
    {
        return (string) $value;
    }

    public function deserialize(string $value): mixed
    {
        if (filter_var($value, \FILTER_VALIDATE_BOOLEAN) === false) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid boolean.', $value));
        }

        return (bool) $value;
    }
}
