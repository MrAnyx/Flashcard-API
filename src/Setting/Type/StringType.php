<?php

declare(strict_types=1);

namespace App\Setting\Type;

class StringType implements SettingTypeInterface
{
    public function serialize(mixed $value, array $options = []): string
    {
        if (!\is_string($value) && null !== $value) {
            throw new \LogicException(\sprintf('Expected type "string", but "%s" given.', \gettype($value)));
        }

        return (string) $value;
    }

    public function deserialize(string $value, array $options = []): string
    {
        return (string) $value;
    }
}
