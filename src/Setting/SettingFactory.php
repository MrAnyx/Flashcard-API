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

        switch ($default->getType()) {
            case SettingType::INTEGER:
                return new IntegerSetting($name, $value);
            case SettingType::FLOAT:
                return new FloatSetting($name, $value);
            case SettingType::STRING:
                return new StringSetting($name, $value);
            case SettingType::BOOLEAN:
                return new BooleanSetting($name, $value);
            default:
                $type = \gettype($value);
                throw new \InvalidArgumentException("Setting value of type {$type} is not supported");
        }
    }
}
