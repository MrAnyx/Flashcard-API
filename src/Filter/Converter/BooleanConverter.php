<?php

declare(strict_types=1);

namespace App\Filter\Converter;

class BooleanConverter implements FilterValueConverterInterface
{
    public function convert(string $value): bool
    {
        if (filter_var($value, \FILTER_VALIDATE_BOOLEAN) === false) {
            throw new \InvalidArgumentException(\sprintf('The filter value "%s" is not a valid boolean.', $value));
        }

        return (bool) $value;
    }
}
