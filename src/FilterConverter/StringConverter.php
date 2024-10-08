<?php

declare(strict_types=1);

namespace App\FilterConverter;

use App\Enum\OperatorType;
use App\Serializer\StringSerializer;

class StringConverter extends StringSerializer implements FilterConverterInterface
{
    /**
     * @return OperatorType[]
     */
    public function getSupportedOperators(): array
    {
        return [
            OperatorType::EQUAL,
            OperatorType::NOT_EQUAL,
            OperatorType::LIKE,
        ];
    }
}
