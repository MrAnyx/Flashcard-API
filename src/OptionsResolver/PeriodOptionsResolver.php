<?php

declare(strict_types=1);

namespace App\OptionsResolver;

use App\Enum\PeriodType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodOptionsResolver extends OptionsResolver
{
    public function configurePeriod(): self
    {
        return $this
            ->setDefined('period')
            ->setAllowedTypes('period', 'string')
            ->setDefault('period', PeriodType::ALL->value)
            ->setAllowedValues('period', PeriodType::values())
            ->setNormalizer('period', fn (Options $options, string $value) => PeriodType::tryFrom($value));
    }
}
