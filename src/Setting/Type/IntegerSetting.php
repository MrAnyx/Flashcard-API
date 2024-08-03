<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Enum\SettingName;
use App\Enum\SettingType;

class IntegerSetting extends AbstractSetting
{
    public function __construct(SettingName $name, int $value)
    {
        parent::__construct($name, $value);
    }

    public function getType(): SettingType
    {
        return SettingType::INTEGER;
    }

    public function serialize(): string
    {
        return (string) $this->value;
    }
}
