<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordRequestDTO
{
    #[Assert\NotBlank]
    #[Groups(['write:passwordReset:request'])]
    public string $identifier;
}
