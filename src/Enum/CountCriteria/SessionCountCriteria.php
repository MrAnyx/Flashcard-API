<?php

declare(strict_types=1);

namespace App\Enum\CountCriteria;

use App\Enum\EnumUtility;

enum SessionCountCriteria: string
{
    use EnumUtility;

    case ALL = 'all';

    case GROUP_BY_DATE = 'group_by_date';
}
