<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Enum\SettingName;
use App\Enum\SettingType;

class IterableSetting extends AbstractSetting
{
    public function __construct(SettingName $name, array|object $value)
    {
        parent::__construct($name, $value);
    }

    public function getType(): SettingType
    {
        return SettingType::ITERABLE;
    }

    public function serialize(): string
    {
        return json_encode($this->value);
    }

    public function isValid(mixed $value): bool
    {
        return \in_array(\gettype($value), ['array', 'object']);
    }
}
