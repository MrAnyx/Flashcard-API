<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TopicDTO
{
    #[Assert\NotBlank(groups: ['post', 'put'])]
    public string $name;

    #[Assert\NotBlank(groups: ['post', 'put'])]
    public string $description;

    #[Assert\NotBlank(groups: ['post', 'put'])]
    public bool $favorite;
}
