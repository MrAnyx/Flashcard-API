<?php

declare(strict_types=1);

namespace App\OptionsResolver;

use App\Enum\SettingName;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingOptionsResolver extends OptionsResolver
{
    public function configureName(): self
    {
        $this
            ->setDefined('name')
            ->setAllowedTypes('name', 'string')
            ->setAllowedValues('name', SettingName::values())
            ->setRequired('name')
            ->setNormalizer('name', fn (Options $options, string $value) => SettingName::tryFrom($value));

        return $this;
    }

    public function configureValue(): self
    {
        $this
            ->setDefined('value')
            ->setRequired('value');

        return $this;
    }
}
