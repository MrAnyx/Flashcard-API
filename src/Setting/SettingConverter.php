<?php

declare(strict_types=1);

namespace App\Setting;

use App\Enum\SettingType;

class SettingConverter
{
    public static function serialize(SettingType $settingType, mixed $value): string
    {
        return match ($settingType) {
            SettingType::BOOLEAN => (string) $value,
            SettingType::FLOAT => (string) $value,
            SettingType::INTEGER => (string) $value,
            SettingType::STRING => (string) $value,
        };
    }

    public static function deserialize(SettingType $settingType, string $value): mixed
    {
        return match ($settingType) {
            SettingType::BOOLEAN => filter_var($value, \FILTER_VALIDATE_BOOLEAN),
            SettingType::FLOAT => (float) $value,
            SettingType::INTEGER => (int) $value,
            SettingType::STRING => (string) $value,
        };
    }
}
