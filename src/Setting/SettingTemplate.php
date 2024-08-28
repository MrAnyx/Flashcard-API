<?php

declare(strict_types=1);

namespace App\Setting;

use App\Enum\SettingName;
use App\Setting\Type\IntegerType;
use App\Setting\Type\StringType;
use Symfony\Component\Validator\Constraints as Assert;

class SettingTemplate
{
    /**
     * @return SettingEntry[]
     */
    public static function getTemplate(): array
    {
        return [
            new SettingEntry(SettingName::ITEMS_PER_PAGE, 50, IntegerType::class, [
                new Assert\Type('integer'),
                new Assert\Range(min: 1, max: 1000),
            ]),
            new SettingEntry(SettingName::FLASHCARD_PER_SESSION, 20, IntegerType::class, [
                new Assert\Type('integer'),
                new Assert\Range(min: 1, max: 50),
            ]),
            new SettingEntry(SettingName::COLOR_THEME, 'light', StringType::class, [
                new Assert\Type('string'),
                new Assert\Choice(['light', 'dark', 'system']),
            ]),
            new SettingEntry(SettingName::PRIMARY_COLOR, 'sky', StringType::class, [
                new Assert\Type('string'),
                new Assert\Choice(['red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose']),
            ]),
            new SettingEntry(SettingName::GRAY_COLOR, 'cool', StringType::class, [
                new Assert\Type('string'),
                new Assert\Choice(['slate', 'cool', 'zinc', 'neutral', 'stone']),
            ]),
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

    /**
     * @return SettingEntry|null Returns the corresponding default setting if it exists, null otherwise
     */
    public static function getSetting(SettingName $name): ?SettingEntry
    {
        foreach (self::getTemplate() as $setting) {
            if ($setting->getName(true) === $name) {
                return $setting;
            }
        }

        return null;
    }
}
