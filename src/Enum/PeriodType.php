<?php

declare(strict_types=1);

namespace App\Enum;

enum PeriodType: string
{
    use EnumUtility;

    case ALL = 'all';

    case TODAY = 'today';

    case YESTERDAY = 'yesterday';

    case LAST_7_DAYS = 'last_7_days';

    case LAST_14_DAYS = 'last_14_days';

    case LAST_30_DAYS = 'last_30_days';

    case LAST_90_DAYS = 'last_90_days';
}
