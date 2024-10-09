<?php

declare(strict_types=1);

namespace App\Serializer;

class StringSerializer implements SerializerInterface
{
    public function canSerialize(mixed $value): bool
    {
        return \is_string($value);
    }

    public function serialize(mixed $value): string
    {
        if (!$this->canSerialize($value)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid string.', $value));
        }

        return (string) $value;
    }

    public function canDeserialize(string $value): bool
    {
        return true;
    }

    public function deserialize(string $value): string
    {
        if (!$this->canDeserialize($value)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid string.', $value));
        }

        return $value;
    }
}
