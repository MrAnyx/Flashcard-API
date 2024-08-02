<?php

declare(strict_types=1);

namespace App\Setting;

use App\Enum\SettingName;
use App\Setting\Type\AbstractSetting;
use App\Setting\Type\BooleanSetting;
use App\Setting\Type\FloatSetting;
use App\Setting\Type\IntegerSetting;
use App\Setting\Type\StringSetting;

class SettingFactory
{
    public static function create(SettingName $name, mixed $value): AbstractSetting
    {
        SettingsTemplate::validateSetting($name, $value);

        $type = \gettype($value);

        switch ($type) {
            case 'integer':
                return new IntegerSetting($name, $value);
            case 'float':
            case 'double':
                return new FloatSetting($name, $value);
            case 'string':
                return new StringSetting($name, $value);
            case 'bool':
                return new BooleanSetting($name, $value);
            default:
                throw new \InvalidArgumentException("Setting value of type {$type} is not supported");
        }
    }
}
