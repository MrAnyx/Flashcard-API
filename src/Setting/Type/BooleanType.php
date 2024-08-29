<?php

declare(strict_types=1);

namespace App\Setting\Type;

class BooleanType extends AbstractSettingType
{
    public function supportedTypes(): array
    {
        return ['boolean'];
    }

    public function serialize(mixed $value, array $options = []): string
    {
        return (string) $value;
    }

    public function deserialize(string $value, array $options = []): bool
    {
        return (bool) $value;
    }

    public function validateOutput(string $value): void
    {
        if (filter_var($value, \FILTER_VALIDATE_BOOLEAN) === null) {
            throw new \InvalidArgumentException(\sprintf('Setting value "%s" is not a valid boolean.', $value));
        }
    }
}
