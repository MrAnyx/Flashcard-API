<?php

declare(strict_types=1);

namespace App\Trait;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validation;

trait UseValidationTrait
{
    /**
     * @param Constraint[] $constraints
     */
    public function validate(mixed $value, array $constraints = []): void
    {
        $validator = Validation::createValidator();
        $errors = $validator->validate($value, $constraints);

        if (\count($errors) > 0) {
            throw new \InvalidArgumentException($errors[0]->getMessage());
        }
    }
}
