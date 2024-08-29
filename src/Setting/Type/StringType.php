<?php

declare(strict_types=1);

namespace App\Setting\Type;

class StringType extends AbstractSettingType
{
    public function supportedTypes(): array
    {
        return ['string'];
    }

    public function serialize(mixed $value, array $options = []): string
    {
        return (string) $value;
    }

    public function deserialize(string $value, array $options = []): string
    {
        return (string) $value;
    }

    public function validateOutput(string $value): void
    {
    }
}
