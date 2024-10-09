<?php

declare(strict_types=1);

namespace App\Serializer;

class IntegerSerializer implements SerializerInterface
{
    public function canSerialize(mixed $value): bool
    {
        return \is_int($value);
    }

    public function serialize(mixed $value): string
    {
        if ($this->canSerialize($value)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid integer.', $value));
        }

        return (string) $value;
    }

    public function canDeserialize(string $value): bool
    {
        return filter_var($value, \FILTER_VALIDATE_INT) !== false;
    }

    public function deserialize(string $value): int
    {
        if (!$this->canDeserialize($value)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid integer.', $value));
        }

        return (int) $value;
    }
}
