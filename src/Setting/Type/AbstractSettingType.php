<?php

declare(strict_types=1);

namespace App\Setting\Type;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;

abstract class AbstractSettingType
{
    /**
     * @return string[] The list of types available
     *
     * @see https://symfony.com/doc/current/reference/constraints/Type.html#type-1
     */
    abstract public function supportedTypes(): array;

    abstract public function serialize(mixed $value, array $options): string;

    abstract public function deserialize(string $value, array $options): mixed;

    /**
     * Validates the database value if it is deserializable.
     */
    abstract public function validateOutput(string $value): void;

    /**
     * Validates the php value if is matchs the required types and some additional constraints.
     *
     * @param Constraint[] $additionalConstraints
     */
    public function validateInput(mixed $value, array $additionalConstraints = []): void
    {
        $constraints = [
            new Type($this->supportedTypes()),
            ...$additionalConstraints,
        ];

        $validator = Validation::createValidator();
        $errors = $validator->validate($value, $constraints);

        if (\count($errors) > 0) {
            throw new \InvalidArgumentException($errors[0]->getMessage());
        }
    }
}
