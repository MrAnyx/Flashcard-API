<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints\Collection;

interface DTO
{
    public function getValidationRules(): Collection;
}
