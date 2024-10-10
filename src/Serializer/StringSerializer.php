<?php

declare(strict_types=1);

namespace App\Serializer;

class StringSerializer implements SerializerInterface
{
    public function canSerialize(mixed $value): void
    {
        if (!\is_string($value)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid string.', $value));
        }
    }

    public function serialize(mixed $value): string
    {
        $this->canSerialize($value);

        return (string) $value;
    }

    public function canDeserialize(string $value): void
    {
    }

    public function deserialize(string $value): string
    {
        $this->canDeserialize($value);

        return $value;
    }
}
