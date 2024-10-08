<?php

declare(strict_types=1);

namespace App\OptionsResolver;

use App\Service\FilterConverter;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterOptionsResolver extends OptionsResolver
{
    public function __construct(
        private FilterConverter $filterConverter,
    ) {
    }

    public function configureFilter(array $searchableFields): self
    {
        return $this
            ->setDefined('filter')
            ->setAllowedTypes('filter', 'string')
            ->setAllowedValues('filter', $searchableFields);
    }

    public function configureValue(string $entityFqcn): self
    {
        return $this
            ->setDefined('value')
            ->setAllowedTypes('value', 'string')
            ->setNormalizer('value', function (Options $options, $rawValue) use ($entityFqcn): mixed {
                if (!isset($options['filter'])) {
                    return null;
                }

                $value = trim($rawValue);

                try {
                    return $this->filterConverter->convert($entityFqcn, $options['filter'], $value);
                } catch (\Exception $e) {
                    return null;
                }
            });
    }
}
