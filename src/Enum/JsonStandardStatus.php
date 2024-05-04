<?php

declare(strict_types=1);

namespace App\Enum;

enum JsonStandardStatus: string
{
    case VALID = 'valid';
    case INVALID = 'invalid';
}
