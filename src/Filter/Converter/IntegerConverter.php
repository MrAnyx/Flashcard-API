<?php

declare(strict_types=1);

namespace App\Filter\Converter;

class IntegerConverter implements FilterValueConverterInterface
{
    public function convert(string $value): int
    {
        if (filter_var($value, \FILTER_VALIDATE_INT) === false) {
            throw new \InvalidArgumentException(\sprintf('The filter value "%s" is not a valid integer.', $value));
        }

        return (int) $value;
    }
}
