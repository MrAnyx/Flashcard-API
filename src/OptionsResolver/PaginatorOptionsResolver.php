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

    public function configureOrder(): self
    {
        return $this
            ->setDefined('order')
            ->setDefault('order', 'ASC')
            ->setAllowedTypes('order', 'string')
            ->setAllowedValues('order', ['ASC', 'DESC']);
    }

    public function configureSort(array $sortableFields): self
    {
        return $this
            ->setDefined('sort')
            ->setDefault('sort', 'id')
            ->setAllowedTypes('sort', 'string')
            ->setAllowedValues('sort', $sortableFields);
    }
}
