<?php

declare(strict_types=1);

namespace App\Setting\Type;

class BooleanType implements SettingTypeInterface
{
    public function getType(): array
    {
        return ['boolean'];
    }

    public function serialize(mixed $value, array $options = []): string
    {
        if (!\is_bool($value) && null !== $value) {
            throw new \LogicException(\sprintf('Expected type "boolean", but "%s" given.', \gettype($value)));
        }

        return (string) $value;
    }

    public function deserialize(string $value, array $options = []): bool
    {
        if (filter_var($value, \FILTER_VALIDATE_BOOLEAN) === null) {
            throw new \InvalidArgumentException(\sprintf('Setting value "%s" is not a valid boolean.', $value));
        }

        return (bool) $value;
    }
}
