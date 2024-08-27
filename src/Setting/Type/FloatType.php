<?php

declare(strict_types=1);

namespace App\Setting\Type;

class FloatType implements SettingTypeInterface
{
    public function serialize(mixed $value, array $options = []): string
    {
        if (!\is_float($value) && null !== $value) {
            throw new \LogicException(\sprintf('Expected type "float", but "%s" given.', \gettype($value)));
        }

        return (string) $value;
    }

    public function deserialize(string $value, array $options = []): float
    {
        if (filter_var($value, \FILTER_VALIDATE_FLOAT) === false) {
            throw new \InvalidArgumentException(\sprintf('Setting value "%s" is not a valid float.', $value));
        }

        return (float) $value;
    }
}
