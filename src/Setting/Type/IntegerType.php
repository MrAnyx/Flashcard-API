<?php

declare(strict_types=1);

namespace App\Setting\Type;

class IntegerType implements SettingTypeInterface
{
    public function getType(): array
    {
        return ['int'];
    }

    public function serialize(mixed $value, array $options = []): string
    {
        if (!\is_int($value) && null !== $value) {
            throw new \LogicException(\sprintf('Expected type "integer", but "%s" given.', \gettype($value)));
        }

        return (string) $value;
    }

    public function deserialize(string $value, array $options = []): int
    {
        if (filter_var($value, \FILTER_VALIDATE_INT) === false) {
            throw new \InvalidArgumentException(\sprintf('Setting value "%s" is not a valid integer.', $value));
        }

        return (int) $value;
    }
}
