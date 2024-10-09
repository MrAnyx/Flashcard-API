<?php

declare(strict_types=1);

namespace App\FilterConverter;

use App\Enum\OperatorType;

interface FilterConverterInterface
{
    /**
     * @return OperatorType[]
     */
    public function getSupportedOperators(): array;

    public function deserialize(string $value): mixed;
}
