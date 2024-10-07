<?php

declare(strict_types=1);

namespace App\Filter\Converter;

interface FilterValueConverterInterface
{
    public function convert(string $value): mixed;
}
