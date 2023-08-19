<?php

namespace App\OptionsResolver;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaginatorOptionsResolver extends OptionsResolver
{
    public function configurePage(): self
    {
        return $this
            ->setDefined('page')
            ->setDefault('page', 1)
            ->setAllowedTypes('page', 'numeric')
            ->setAllowedValues('page', function ($page) {
                $validatedValue = filter_var($page, FILTER_VALIDATE_INT, [
                    'flags' => FILTER_NULL_ON_FAILURE,
                ]);

                if (null === $validatedValue || $validatedValue < 1) {
                    return false;
                }

                return true;
            })
            ->setNormalizer('page', fn (Options $options, $page) => (int) $page);
    }
}
