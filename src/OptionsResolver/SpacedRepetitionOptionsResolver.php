<?php

namespace App\OptionsResolver;

use App\Enum\GradeType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpacedRepetitionOptionsResolver extends OptionsResolver
{
    public function configureQuality(): self
    {
        return $this
            ->setDefined('quality')
            ->setRequired('quality')
            ->setAllowedTypes('quality', 'int')
            ->setAllowedValues('quality', function ($quality) {
                if ($quality < 0 || $quality > 5) {
                    return false;
                }

                return true;
            })
            ->setNormalizer('quality', function (Options $options, int $value) {
                return GradeType::tryFrom($value);
            });
    }
}
