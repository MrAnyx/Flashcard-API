<?php

declare(strict_types=1);

namespace App\FilterConverter;

use App\Enum\OperatorType;
use App\Serializer\IntegerSerializer;

class IntegerConverter extends IntegerSerializer implements FilterConverterInterface
{
    /**
     * @return App\Enum\OperatorType[]
     */
    public function getSupportedOperators(): array
    {
        return [
            OperatorType::EQUAL,
            OperatorType::NOT_EQUAL,
            OperatorType::GREATER_THAN,
            OperatorType::GREATER_THAN_OR_EQUAL,
            OperatorType::LESS_THAN,
            OperatorType::LESS_THAN_OR_EQUAL,
        ];
    }
}
