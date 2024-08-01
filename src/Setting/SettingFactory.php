<?php

declare(strict_types=1);

namespace App\Setting;

use App\Enum\SettingName;
use App\Setting\Type\AbstractSetting;
use App\Setting\Type\BooleanSetting;
use App\Setting\Type\FloatSetting;
use App\Setting\Type\IntegerSetting;
use App\Setting\Type\IterableSetting;
use App\Setting\Type\MixedSetting;
use App\Setting\Type\StringSetting;

class SettingFactory
{
    public static function create(SettingName $name, mixed $value): AbstractSetting
    {
        // $default =

        switch (\gettype($value)) {
            case 'int':
                return new IntegerSetting($name, $value);
            case 'float':
            case 'double':
                return new FloatSetting($name, $value);
            case 'string':
                return new StringSetting($name, $value);
            case 'bool':
                return new BooleanSetting($name, $value);
            case 'array':
            case 'object':
                return new IterableSetting($name, $value);
            default:
                return new MixedSetting($name, $value);
        }
    }
}
