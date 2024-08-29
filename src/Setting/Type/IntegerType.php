<?php

declare(strict_types=1);

namespace App\Setting\Type;

class IntegerType extends AbstractSettingType
{
    public function supportedTypes(): array
    {
        return ['int'];
    }

    /**
     * @param int $value
     */
    public function serialize(mixed $value, array $options = []): string
    {
        return (string) $value;
    }

    public function deserialize(string $value, array $options = []): int
    {
        return (int) $value;
    }

    public function validateOutput(string $value): void
    {
        if (filter_var($value, \FILTER_VALIDATE_INT) === false) {
            throw new \InvalidArgumentException(\sprintf('Setting value "%s" is not a valid boolean.', $value));
        }
    }
}
