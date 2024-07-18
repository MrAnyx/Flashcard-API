<?php

declare(strict_types=1);

namespace App\OptionsResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

class UserOptionsResolver extends OptionsResolver
{
    public function configureUsername(bool $isRequired = true): self
    {
        $this->setDefined('username')->setAllowedTypes('username', 'string');

        if ($isRequired) {
            $this->setRequired('username');
        }

        return $this;
    }

    public function configureEmail(bool $isRequired = true): self
    {
        $this->setDefined('identifier')->setAllowedTypes('identifier', 'string');

        if ($isRequired) {
            $this->setRequired('identifier');
        }

        return $this;
    }

    public function configurePassword(bool $isRequired = true): self
    {
        $this->setDefined('password')->setAllowedTypes('password', 'string');

        if ($isRequired) {
            $this->setRequired('password');
        }

        return $this;
    }

    public function configureRoles(bool $isRequired = true): self
    {
        $this->setDefined('roles')->setAllowedTypes('roles', 'string[]');

        if ($isRequired) {
            $this->setRequired('roles');
        }

        return $this;
    }
}
