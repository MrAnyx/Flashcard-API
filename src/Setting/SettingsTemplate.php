<?php

declare(strict_types=1);

namespace App\Setting;

use App\Enum\SettingName;
use App\Setting\Type\AbstractSetting;
use App\Setting\Type\IntegerSetting;
use App\Setting\Type\StringSetting;

class SettingsTemplate
{
    /**
     * @return AbstractSetting[]
     */
    public static function getTemplate(): array
    {
        return [
            new IntegerSetting(SettingName::ITEMS_PER_PAGE, 50),
            new IntegerSetting(SettingName::FLASHCARD_PER_SESSION, 20),
            new StringSetting(SettingName::COLOR_THEME, 'light', ['light', 'dark', 'system']),
            new StringSetting(SettingName::PRIMARY_COLOR, 'sky', ['red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose']),
            new StringSetting(SettingName::GRAY_COLOR, 'cool', ['slate', 'cool', 'zinc', 'neutral', 'stone']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function getAssociativeTemplate(): array
    {
        $template = [];

        foreach (self::getTemplate() as $setting) {
            $template[$setting->name->value] = $setting->value;
        }

        return $template;
    }

    /**
     * @return AbstractSetting|null Returns the corresponding default setting if it exists, null otherwise
     */
    public static function getSetting(SettingName $name): ?AbstractSetting
    {
        foreach (self::getTemplate() as $setting) {
            if ($setting->name === $name) {
                return $setting;
            }
        }

        return null;
    }

    /**
     * Returns the template setting.
     */
    public static function validateSetting(SettingName $settingName, mixed $value): AbstractSetting
    {
        $setting = self::getSetting($settingName);

        if ($setting === null) {
            throw new \InvalidArgumentException("Setting {$settingName->value} is not defined.");
        }

        if (!$setting->isValid($value)) {
            $valueType = \gettype($value);

            throw new \InvalidArgumentException("Setting {$settingName->value} expects value of type {$setting->getType()->value}, {$valueType} given");
        }

        return $setting;
    }
}
