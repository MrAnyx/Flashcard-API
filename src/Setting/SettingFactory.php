<?php

declare(strict_types=1);

namespace App\Setting;

use App\Enum\SettingName;
use App\Enum\SettingType;
use App\Setting\Type\AbstractSetting;
use App\Setting\Type\BooleanSetting;
use App\Setting\Type\FloatSetting;
use App\Setting\Type\IntegerSetting;
use App\Setting\Type\StringSetting;

class SettingFactory
{
    public static function create(SettingName $name, mixed $value): AbstractSetting
    {
        $default = SettingsTemplate::validateSetting($name, $value);

        return match ($default->getType()) {
            SettingType::INTEGER => new IntegerSetting($name, $value),
            SettingType::FLOAT => new FloatSetting($name, $value),
            SettingType::STRING => new StringSetting($name, $value),
            SettingType::BOOLEAN => new BooleanSetting($name, $value),
        };
    }
}
