<?php

declare(strict_types=1);

namespace App\Serializer;

class IntegerSerializer implements SerializerInterface
{
    public function canSerialize(mixed $value): void
    {
        if (!\is_int($value)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid integer.', $value));
        }
    }

    public function serialize(mixed $value): string
    {
        $this->canSerialize($value);

        return (string) $value;
    }

    public function canDeserialize(string $value): void
    {
        if (filter_var($value, \FILTER_VALIDATE_INT, \FILTER_NULL_ON_FAILURE) === null) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid integer.', $value));
        }
    }

    public function deserialize(string $value): int
    {
        $this->canDeserialize($value);

        return (int) $value;
    }
}
