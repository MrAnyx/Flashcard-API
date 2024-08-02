<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Enum\SettingName;
use App\Enum\SettingType;

class FloatSetting extends AbstractSetting
{
    public function __construct(SettingName $name, float $value)
    {
        parent::__construct($name, $value);
    }

    public function getType(): SettingType
    {
        return SettingType::FLOAT;
    }

    public function serialize(): string
    {
        return (string) $this->value;
    }

    public function isValid(mixed $value): bool
    {
        return \in_array(\gettype($value), ['float', 'double']);
    }
}
