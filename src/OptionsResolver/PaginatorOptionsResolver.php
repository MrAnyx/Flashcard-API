<?php

declare(strict_types=1);

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
                $validatedValue = filter_var($page, \FILTER_VALIDATE_INT, [
                    'flags' => \FILTER_NULL_ON_FAILURE,
                ]);

                if ($validatedValue === null || $validatedValue < 1) {
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
            ->setAllowedValues('order', ['ASC', 'asc', 'DESC', 'desc']);
    }

    public function configureSort(array $sortableFields): self
    {
        return $this
            ->setDefined('sort')
            ->setDefault('sort', 'id')
            ->setAllowedTypes('sort', 'string')
            ->setAllowedValues('sort', $sortableFields);
    }

    public function configureItemsPerPage(): self
    {
        return $this
            ->setDefined('itemsPerPage')
            ->setDefault('itemsPerPage', '25') // We use string even if it a number because it comes from the url
            ->setAllowedTypes('itemsPerPage', 'numeric')
            ->setAllowedValues('itemsPerPage', ['25', '50', '100', '200'])
            ->setNormalizer('itemsPerPage', fn (Options $options, $itemsPerPage) => (int) $itemsPerPage);
    }
}
