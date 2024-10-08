<?php

declare(strict_types=1);

namespace App\Enum;

enum OperatorType: string
{
    use EnumUtility;

    case EQUAL = 'eq';

    case NOT_EQUAL = 'neq';

    case LESS_THAN = 'lt';

    case LESS_THAN_OR_EQUAL = 'lte';

    case GREATER_THAN = 'gt';

    case GREATER_THAN_OR_EQUAL = 'gte';

    case LIKE = 'like';

    public function getDoctrineNotation(): string
    {
        return match ($this) {
            self::EQUAL => '=',
            self::NOT_EQUAL => '!=',
            self::LESS_THAN => '<',
            self::LESS_THAN_OR_EQUAL => '<=',
            self::GREATER_THAN => '>',
            self::GREATER_THAN_OR_EQUAL => '>=',
            self::LIKE => 'LIKE',
        };
    }
}
