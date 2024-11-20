<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordResetDTO
{
    #[Assert\NotBlank]
    #[Groups(['write:passwordReset:proceed'])]
    public string $token;

    #[Assert\NotBlank]
    #[Groups(['write:passwordReset:proceed'])]
    public string $rawPassword;
}
