<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Enum\SettingName;

abstract class AbstractSetting
{
    public function __construct(
        public readonly SettingName $name,
        public readonly mixed $value
    ) {
    }

    /**
     * You can pass any type for which an `is_<type>()` function is defined in PHP.
     *
     * @see https://www.php.net/manual/fr/ref.var.php
     */
    abstract public function getType(): string;

    abstract public function serialize(): string;

    public function isValid(mixed $value): bool
    {
        $isValidMethod = "is_{$this->getType()}";

        if (!\function_exists($isValidMethod)) {
            throw new \InvalidArgumentException("Type {$this->getType()} is not valid.");
        }

        return $isValidMethod($value);
    }
}
