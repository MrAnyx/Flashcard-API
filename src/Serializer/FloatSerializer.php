<?php

declare(strict_types=1);

namespace App\Serializer;

class FloatSerializer implements SerializerInterface
{
    public function canSerialize(mixed $value): bool
    {
        return \is_float($value);
    }

    public function serialize(mixed $value): string
    {
        if (!$this->canSerialize($value)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid float.', $value));
        }

        return (string) $value;
    }

    public function canDeserialize(string $value): bool
    {
        return filter_var($value, \FILTER_VALIDATE_FLOAT) !== false;
    }

    public function deserialize(string $value): float
    {
        if (!$this->canDeserialize($value)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid float.', $value));
        }

        return (float) $value;
    }
}
