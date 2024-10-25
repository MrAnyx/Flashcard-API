<?php

declare(strict_types=1);

namespace App\Serializer;

class BooleanSerializer implements SerializerInterface
{
    public function canSerialize(mixed $value): void
    {
        if (!\is_bool($value)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid boolean.', $value));
        }
    }

    public function serialize(mixed $value): string
    {
        $this->canSerialize($value);

        return (string) $value;
    }

    public function canDeserialize(string $value): void
    {
        if (filter_var($value, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE) === null) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid boolean.', $value));
        }
    }

    public function deserialize(string $value): mixed
    {
        $this->canDeserialize($value);

        return (bool) $value;
    }
}
