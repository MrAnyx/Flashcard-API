<?php

declare(strict_types=1);

namespace App\Serializer;

class BooleanSerializer implements SerializerInterface
{
    public function canSerialize(mixed $value): bool
    {
        return \is_bool($value);
    }

    public function serialize(mixed $value): string
    {
        if (!$this->canSerialize($value)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid boolean.', $value));
        }

        return (string) $value;
    }

    public function canDeserialize(string $value): bool
    {
        return filter_var($value, \FILTER_VALIDATE_BOOLEAN) !== false;
    }

    public function deserialize(string $value): mixed
    {
        if (!$this->canDeserialize($value)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid boolean.', $value));
        }

        return (bool) $value;
    }
}
