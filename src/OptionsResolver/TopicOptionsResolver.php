<?php

namespace App\OptionsResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

class TopicOptionsResolver extends OptionsResolver
{
    public function configureName(bool $isRequired = true): self
    {
        $this->setDefined('name')->setAllowedTypes('name', 'string');

        if ($isRequired) {
            $this->setRequired('name');
        }

        return $this;
    }

    public function configureAuthor(bool $isRequired = true): self
    {
        $this->setDefined('author')->setAllowedTypes('author', 'int');

        if ($isRequired) {
            $this->setRequired('author');
        }

        return $this;
    }

    public function configureAll(bool $isRequired = true): self
    {
        $this
            ->configureName($isRequired)
            ->configureAuthor($isRequired);

        return $this;
    }
}
