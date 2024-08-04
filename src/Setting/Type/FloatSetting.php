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
}
