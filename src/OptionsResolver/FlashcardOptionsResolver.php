<?php

declare(strict_types=1);

namespace App\OptionsResolver;

use App\Repository\UnitRepository;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FlashcardOptionsResolver extends OptionsResolver
{
    public function __construct(
        private UnitRepository $unitRepository,
    ) {
    }

    public function configureFront(bool $isRequired = true): self
    {
        $this->setDefined('front')->setAllowedTypes('front', 'string');

        if ($isRequired) {
            $this->setRequired('front');
        }

        return $this;
    }

    public function configureBack(bool $isRequired = true): self
    {
        $this->setDefined('back')->setAllowedTypes('back', 'string');

        if ($isRequired) {
            $this->setRequired('back');
        }

        return $this;
    }

    public function configureDetails(bool $isRequired = true): self
    {
        $this->setDefined('details')->setAllowedTypes('details', ['string', 'null']);

        if ($isRequired) {
            $this->setRequired('details');
        }

        $this->addNormalizer('details', function (Options $options, $value) {
            $result = trim($value);

            return $result === '' ? null : $result;
        });

        return $this;
    }

    public function configureUnit(bool $isRequired = true): self
    {
        $this->setDefined('unit')->setAllowedTypes('unit', 'int');

        if ($isRequired) {
            $this->setRequired('unit');
        }

        $this->setNormalizer('unit', function (Options $options, $value) {
            $unit = $this->unitRepository->find($value);

            if ($unit === null) {
                throw new InvalidOptionsException("Unit with id {$value} was not found");
            }

            return $unit;
        });

        return $this;
    }

    public function configureFavorite(bool $isRequired = true): self
    {
        $this->setDefined('favorite')->setAllowedTypes('favorite', 'bool');

        if ($isRequired) {
            $this->setRequired('favorite');
        }

        return $this;
    }

    public function configureHelp(bool $isRequired = true): self
    {
        $this->setDefined('help')->setAllowedTypes('help', ['string', 'null']);

        if ($isRequired) {
            $this->setRequired('help');
        }

        $this->addNormalizer('help', function (Options $options, $value) {
            $result = trim($value);

            return $result === '' ? null : $result;
        });

        return $this;
    }
}
