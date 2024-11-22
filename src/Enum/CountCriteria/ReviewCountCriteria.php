<?php

declare(strict_types=1);

namespace App\Enum\CountCriteria;

use App\Enum\EnumUtility;

enum ReviewCountCriteria: string
{
    use EnumUtility;

    case ALL = 'all';

    case ONLY_VALID = 'only-valid';

    case GROUP_BY_DATE = 'group-by-date';
}
