<?php

declare(strict_types=1);

namespace App\Setting\Type;

class FloatType extends AbstractSettingType
{
    public function supportedTypes(): array
    {
        return ['float'];
    }

    public function serialize(mixed $value, array $options = []): string
    {
        return (string) $value;
    }

    public function deserialize(string $value, array $options = []): float
    {
        return (float) $value;
    }

    public function validateOutput(string $value): void
    {
        if (filter_var($value, \FILTER_VALIDATE_FLOAT) === false) {
            throw new \InvalidArgumentException(\sprintf('Setting value "%s" is not a valid float.', $value));
        }
    }
}
