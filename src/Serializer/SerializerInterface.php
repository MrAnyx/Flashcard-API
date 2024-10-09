<?php

declare(strict_types=1);

namespace App\Serializer;

interface SerializerInterface
{
    public function canSerialize(mixed $value): bool;

    public function serialize(mixed $value): string;

    public function canDeserialize(string $value): bool;

    public function deserialize(string $value): mixed;
}
