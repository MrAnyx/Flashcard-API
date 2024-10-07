<?php

declare(strict_types=1);

namespace App\Filter\Converter;

class StringConverter implements FilterValueConverterInterface
{
    public function convert(string $value): string
    {
        return $value;
    }
}
