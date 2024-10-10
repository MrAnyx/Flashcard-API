<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validation;

abstract class AbstractSettingType
{
    /**
     * @param Constraint[] $constraints
     */
    public function __construct(
        protected readonly SerializerInterface $serializer,
        protected readonly array $constraints,
    ) {
    }

    public function canSerializer(mixed $value): void
    {
        $this->serializer->canSerialize($value);

        $validator = Validation::createValidator();
        $errors = $validator->validate($value, $this->constraints);

        if (\count($errors) > 0) {
            throw new \InvalidArgumentException($errors[0]->getMessage());
        }
    }
}
