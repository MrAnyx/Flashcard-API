<?php

declare(strict_types=1);

namespace App\FilterConverter;

use App\Enum\OperatorType;
use App\Serializer\BooleanSerializer;

class BooleanConverter extends BooleanSerializer implements FilterConverterInterface
{
    /**
     * @return OperatorType[]
     */
    public function getSupportedOperators(): array
    {
        return [
            OperatorType::EQUAL,
            OperatorType::NOT_EQUAL,
        ];
    }
}
