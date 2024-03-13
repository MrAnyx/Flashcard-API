<?php

declare(strict_types=1);

namespace App\OptionsResolver;

use App\Enum\GradeType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpacedRepetitionOptionsResolver extends OptionsResolver
{
    public function configureGrade(): self
    {
        return $this
            ->setDefined('grade')
            ->setRequired('grade')
            ->setAllowedTypes('grade', 'int')
            ->setAllowedValues('grade', function ($grade) {
                if ($grade < 1 || $grade > 4) {
                    return false;
                }

                return true;
            })
            ->setNormalizer('grade', function (Options $options, int $value) {
                return GradeType::tryFrom($value);
            });
    }
}
