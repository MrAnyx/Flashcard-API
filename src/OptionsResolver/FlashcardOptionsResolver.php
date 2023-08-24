<?php

namespace App\OptionsResolver;

use App\Repository\UserRepository;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class FlashcardOptionsResolver extends OptionsResolver
{
    public function __construct(
        private UserRepository $userRepository
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
            return $value === '' ? null : $value;
        });

        return $this;
    }

    public function configureAuthor(bool $isRequired = true): self
    {
        $this->setDefined('author')->setAllowedTypes('author', 'int');

        if ($isRequired) {
            $this->setRequired('author');
        }

        $this->setNormalizer('author', function (Options $options, $value) {
            $user = $this->userRepository->find($value);

            if ($user === null) {
                throw new InvalidOptionsException("User with id $value was not found");
            }

            return $user;
        });

        return $this;
    }

    public function configureAll(bool $isRequired = true): self
    {
        $this
            ->configureFront($isRequired)
            ->configureBack($isRequired)
            ->configureDetails($isRequired)
            ->configureAuthor($isRequired);

        return $this;
    }
}
