<?php

declare(strict_types=1);

namespace App\Serializer;

class FloatSerializer implements SerializerInterface
{
    public function canSerialize(mixed $value): void
    {
        if (!\is_float($value)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid float.', $value));
        }
    }

    public function serialize(mixed $value): string
    {
        $this->canSerialize($value);

        return (string) $value;
    }

    public function canDeserialize(string $value): void
    {
        if (filter_var($value, \FILTER_VALIDATE_FLOAT, \FILTER_NULL_ON_FAILURE) === null) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid float.', $value));
        }
    }

    public function deserialize(string $value): float
    {
        $this->canDeserialize($value);

        return (float) $value;
    }
}
