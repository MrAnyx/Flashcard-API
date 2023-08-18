<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserDTO implements DTO
{
    public string $username;

    public string $email;

    public string $password;

    public array $roles = ['ROLE_USER'];

    public function getValidationRules(): Assert\Collection
    {
        return new Assert\Collection([
            'username' => [
                new Assert\NotBlank(),
            ],
        ]);
    }
}
