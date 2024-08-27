<?php

declare(strict_types=1);

namespace App\Setting\Type;

interface SettingTypeInterface
{
    public function serialize(mixed $value, array $options): string;

    public function deserialize(string $value, array $options): mixed;
}
