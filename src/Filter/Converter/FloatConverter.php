<?php

declare(strict_types=1);

namespace App\Filter\Converter;

class FloatConverter implements FilterValueConverterInterface
{
    public function convert(string $value): float
    {
        if (filter_var($value, \FILTER_VALIDATE_FLOAT) === false) {
            throw new \InvalidArgumentException(\sprintf('The filter value "%s" is not a valid float.', $value));
        }

        return (float) $value;
    }
}
