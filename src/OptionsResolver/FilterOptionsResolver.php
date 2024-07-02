<?php

declare(strict_types=1);

namespace App\OptionsResolver;

use App\Model\TypedField;
use App\Service\TypeConverter;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterOptionsResolver extends OptionsResolver
{
    public function configureFilter(array $searchableFields): self
    {
        return $this
            ->setDefined('filter')
            ->setAllowedTypes('filter', 'string')
            ->setAllowedValues('filter', $searchableFields);
    }

    /**
     * @param TypedField[] $typedFields
     */
    public function configureQuery(array $typedFields): self
    {
        return $this
            ->setDefined('query')
            ->setAllowedTypes('query', 'string')
            ->setNormalizer('query', function (Options $options, $query) use ($typedFields): mixed {
                if (!isset($options['filter'])) {
                    return null;
                }

                $filterdTypes = array_filter($typedFields, fn (TypedField $field) => $field->name === $options['filter']);

                $correspondingField = reset($filterdTypes);

                if (!$correspondingField) {
                    return null;
                }

                $value = trim($query);

                try {
                    return TypeConverter::convertStringToType($value, $correspondingField->type);
                } catch (\Exception $e) {
                    return null;
                }
            });
    }
}
