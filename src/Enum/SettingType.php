<?php

declare(strict_types=1);

namespace App\Enum;

enum SettingType: string
{
    case STRING = 'string';
    case INTEGER = 'integer';
    case FLOAT = 'float';
    case BOOLEAN = 'boolean';
    case MIXED = 'mixed';
    case ITERABLE = 'iterable';
}
