<?php

namespace App\OptionsResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

class UserOptionsResolver extends OptionsResolver
{
    public function configureEmail(bool $isRequired = true): self
    {
        $this->setDefined('email')->setAllowedTypes('email', 'int');

        if ($isRequired) {
            $this->setRequired('email');
        }

        return $this;
    }

    public function configureUsername(bool $isRequired = true): self
    {
        $this->setDefined('username')->setAllowedTypes('username', 'string');

        if ($isRequired) {
            $this->setRequired('username');
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

    public function configurePassword(bool $isRequired = true): self
    {
        $this->setDefined('password')->setAllowedTypes('password', 'string');

        if ($isRequired) {
            $this->setRequired('password');
        }

        return $this;
    }
}
