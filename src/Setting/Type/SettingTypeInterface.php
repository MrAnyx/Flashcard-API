<?php

declare(strict_types=1);

namespace App\Setting\Type;

interface SettingTypeInterface
{
    /**
     * @return string[] The list of types available
     *
     * @see https://symfony.com/doc/current/reference/constraints/Type.html#type-1
     */
    public function getType(): array;

    public function serialize(mixed $value, array $options): string;

    public function deserialize(string $value, array $options): mixed;
}
