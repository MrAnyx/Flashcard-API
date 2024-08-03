<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * You can pass any type for which an `is_<type>()` function is defined in PHP.
 *
 * @see https://www.php.net/manual/fr/ref.var.php
 */
enum SettingType: string
{
    case BOOLEAN = 'bool';
    case INTEGER = 'int';
    case FLOAT = 'float';
    case STRING = 'string';
}
