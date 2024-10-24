<?php

declare(strict_types=1);

namespace App\Enum\CountCriteria;

use App\Enum\EnumUtility;

enum FlashcardCountCriteria: string
{
    use EnumUtility;

    case ALL = 'all';

    case TO_REVIEW = 'to_review';

    case CORRECT = 'correct';
}
