<?php

declare(strict_types=1);

namespace App\Setting;

use App\Enum\SettingName;
use App\Serializer\BooleanSerializer;
use App\Serializer\IntegerSerializer;
use App\Serializer\StringSerializer;
use Symfony\Component\Validator\Constraints as Assert;

class SettingTemplate
{
    /**
     * @return SettingEntry[]
     */
    public static function getTemplate(): array
    {
        return [
            new SettingEntry(SettingName::ITEMS_PER_PAGE, 50, IntegerSerializer::class, [
                new Assert\Range(min: 1, max: 1000),
            ]),
            new SettingEntry(SettingName::FLASHCARD_PER_SESSION, 20, IntegerSerializer::class, [
                new Assert\Range(min: 1, max: 50),
            ]),
            new SettingEntry(SettingName::COLOR_THEME, 'light', StringSerializer::class, [
                new Assert\Choice(['light', 'dark', 'system']),
            ]),
            new SettingEntry(SettingName::PRIMARY_COLOR, 'sky', StringSerializer::class, [
                new Assert\Choice(['red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose']),
            ]),
            new SettingEntry(SettingName::GRAY_COLOR, 'cool', StringSerializer::class, [
                new Assert\Choice(['slate', 'cool', 'zinc', 'neutral', 'stone']),
            ]),
            new SettingEntry(SettingName::SHOW_SESSION_INTRODUCTION, true, BooleanSerializer::class),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function getAssociativeTemplate(): array
    {
        $template = [];

        foreach (self::getTemplate() as $setting) {
            $template[$setting->getName()] = $setting->getValue();
        }

        return $template;
    }

    public static function getSetting(SettingName $name): SettingEntry
    {
        $settings = self::getTemplate();

        foreach ($settings as $setting) {
            if ($setting->getName(true) === $name) {
                return $setting;
            }
        }

        throw new \InvalidArgumentException(\sprintf('Unknown setting name %s, allowed setting name are %s', $name->value, implode(', ', array_keys($settings))));
    }
}
