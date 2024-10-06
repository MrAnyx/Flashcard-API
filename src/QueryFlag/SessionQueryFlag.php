<?php

declare(strict_types=1);

namespace App\QueryFlag;

enum SessionQueryFlag: string
{
    case INCLUDE_TOTAL_REVIEWS = 'include_total_reviews';
}
