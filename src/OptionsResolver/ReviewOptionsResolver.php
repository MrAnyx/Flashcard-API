<?php

declare(strict_types=1);

namespace App\OptionsResolver;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewOptionsResolver extends OptionsResolver
{
    public function configureWithReset(bool $isRequired = true): self
    {
        $this
            ->setDefined('withReset')
            ->setAllowedTypes('withReset', 'string') // We use string even if it a boolean because it comes from the url
            ->setDefault('withReset', false)
            ->setNormalizer('withReset', function (Options $options, string $value) {
                return filter_var($value, \FILTER_VALIDATE_BOOLEAN);
            });

        if ($isRequired) {
            $this->setRequired('withReset');
        }

        return $this;
    }
}
