<?php

declare(strict_types=1);

namespace App\Enum;

enum ContentType: string
{
    use EnumUtility;

    case JSON = 'application/json';

    case JSON_STD = 'application/json+std';
}
