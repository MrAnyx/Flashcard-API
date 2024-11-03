<?php

declare(strict_types=1);

namespace App\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ValidClassPropertyValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidClassProperty) {
            throw new UnexpectedTypeException($constraint, ValidClassProperty::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!class_exists($constraint->classname)) {
            throw new UnexpectedTypeException($constraint->classname, 'valid classname');
        }

        if (!property_exists($constraint->classname, $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ classname }}', $constraint->classname)
                ->setParameter('{{ property }}', $value)
                ->addViolation();
        }
    }
}
