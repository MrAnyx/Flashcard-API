<?php

declare(strict_types=1);

namespace App\Enum;

trait EnumUtility
{
    /**
     * @return string[]
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * @return array<int|string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string, int|string>
     */
    public static function array(): array
    {
        return array_combine(self::names(), self::values());
    }

    public static function arrayCases(): array
    {
        return array_combine(self::names(), self::cases());
    }

    public static function hasValue(int|string $value): bool
    {
        return \in_array($value, self::values());
    }
}
