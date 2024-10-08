<?php

declare(strict_types=1);

namespace App\FilterConverter;

interface FilterConverterInterface
{
    /**
     * @return App\Enum\OperatorType[]
     */
    public function getSupportedOperators(): array;

    public function deserialize(string $value): mixed;
}
