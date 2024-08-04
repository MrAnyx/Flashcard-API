<?php

declare(strict_types=1);

namespace App\OptionsResolver;

use App\Enum\SettingName;
use App\Setting\SettingsTemplate;
use App\Setting\Type\AbstractSetting;
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
        $availableTypes = array_unique(array_map(
            fn (AbstractSetting $el) => $el->getType()->value,
            SettingsTemplate::getTemplate()
        ));

        $this
            ->setDefined('value')
            ->setAllowedTypes('value', $availableTypes)
            ->setRequired('value');

        return $this;
    }
}
