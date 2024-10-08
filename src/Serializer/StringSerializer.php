<?php

declare(strict_types=1);

namespace App\Serializer;

class StringSerializer implements SerializerInterface
{
    public function serialize(mixed $value): string
    {
        return (string) $value;
    }

    public function deserialize(string $value): string
    {
        return $value;
    }
}
