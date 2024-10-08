<?php

declare(strict_types=1);

namespace App\Enum;

enum JsonStandardStatus: string
{
    use EnumUtility;

    case VALID = 'valid';

    case INVALID = 'invalid';
}
