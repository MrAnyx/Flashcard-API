<?php

declare(strict_types=1);

namespace App\Setting;

use App\Enum\SettingName;
use App\Setting\Type\AbstractSetting;
use App\Setting\Type\IntegerSetting;

class SettingsTemplate
{
    /**
     * @return AbstractSetting[]
     */
    public static function getTemplate(): array
    {
        return [
            new IntegerSetting(SettingName::ITEMS_PER_PAGE, 50),
            new IntegerSetting(SettingName::FLASHCARD_PER_SESSION, 10),
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

    public static function validateSetting(SettingName $settingName, mixed $value): void
    {
        $setting = self::getSetting($settingName);

        if ($setting === null) {
            throw new \InvalidArgumentException("Setting {$settingName->value} is not defined.");
        }

        if (!$setting->isValid($value)) {
            $valueType = \gettype($value);

            throw new \InvalidArgumentException("Setting {$settingName->value} expects value of type ({$setting->getType()}), {$valueType} given");
        }
    }
}