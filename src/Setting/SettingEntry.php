<?php

declare(strict_types=1);

namespace App\Setting;

use App\Enum\SettingName;
use App\Serializer\SerializerInterface;
use App\Trait\UseValidationTrait;
use Symfony\Component\Validator\Constraint;

class SettingEntry
{
    use UseValidationTrait;

    private mixed $value;

    /**
     * @param Constraint[] $constraints
     */
    public function __construct(
        public readonly SettingName $name,
        mixed $value,
        public readonly SerializerInterface $serializer,
        /** @var Constraint[] */
        public readonly array $constraints = [],
    ) {
        $this->setValue($value);
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): self
    {
        $this->validateMixedValue($value);

        $this->value = $value;

        return $this;
    }

    public function serialize(): string
    {
        $this->validateMixedValue($this->value);

        return $this->serializer->serialize($this->value);
    }

    public function deserialize(string $rawValue): mixed
    {
        try {
            $this->validateStringValue($rawValue);

            return $this->serializer->deserialize($rawValue);
        } catch (\Exception) {
            return $this->value;
        }
    }

    private function validateMixedValue(mixed $value): void
    {
        $this->serializer->canSerialize($value);
        $this->validate($value, $this->constraints);
    }

    private function validateStringValue(mixed $rawValue): void
    {
        $this->serializer->canDeserialize($rawValue);
        $value = $this->serializer->deserialize($rawValue);

        $this->validate($value, $this->constraints);
    }
}
