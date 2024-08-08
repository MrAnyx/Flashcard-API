<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Enum\SettingName;
use App\Enum\SettingType;

class FloatSetting extends AbstractSetting
{
    public function __construct(SettingName $name, float $value, int|float|string|array|null $allowedValues = null)
    {
        parent::__construct($name, $value, $allowedValues);
    }

    public function getType(): SettingType
    {
        return SettingType::FLOAT;
    }
}
