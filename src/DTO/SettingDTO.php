<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enum\SettingName;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SettingDTO
{
    #[Assert\NotBlank]
    #[Groups(['write:setting:user'])]
    public SettingName $name;

    #[Assert\NotNull]
    #[Groups(['write:setting:user'])]
    public mixed $value;
}
