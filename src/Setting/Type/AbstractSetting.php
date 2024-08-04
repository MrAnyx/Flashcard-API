<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Enum\SettingName;
use App\Enum\SettingType;
use App\Setting\SettingConverter;

abstract class AbstractSetting
{
    public function __construct(
        public readonly SettingName $name,
        public readonly mixed $value
    ) {
    }

    abstract public function getType(): SettingType;

    public function serialize(): string
    {
        return SettingConverter::serialize($this->getType(), $this->value);
    }

    public function isValid(mixed $value): bool
    {
        $isValidMethod = "is_{$this->getType()->value}";

        if (!\function_exists($isValidMethod)) {
            throw new \InvalidArgumentException("Type {$this->getType()->value} is not valid.");
        }

        return $isValidMethod($value);
    }
}
