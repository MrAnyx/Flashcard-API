<?php

declare(strict_types=1);

namespace App\OptionsResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordResetOptionsResolver extends OptionsResolver
{
    public function configureToken(bool $isRequired = true): self
    {
        $this->setDefined('token')->setAllowedTypes('token', 'string');

        if ($isRequired) {
            $this->setRequired('token');
        }

        return $this;
    }
}
