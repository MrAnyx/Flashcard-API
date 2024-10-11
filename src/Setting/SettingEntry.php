<?php

declare(strict_types=1);

namespace App\Setting;

use App\Enum\SettingName;
use App\Serializer\SerializerInterface;
use App\Service\ObjectInitializer;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validation;

class SettingEntry
{
    public readonly SerializerInterface $serializer;

    /**
     * @var Constraint[]
     */
    private readonly array $constraints;

    private readonly SettingName $name;

    private mixed $value;

    /**
     * @param Constraint[] $constraints
     */
    public function __construct(SettingName $name, mixed $defaultValue, string $serializer, array $serializerConstructorArgs = [], array $constraints = [])
    {
        $this->name = $name;
        $this->value = $defaultValue;

        if (!is_a($serializer, SerializerInterface::class, true)) {
            throw new \InvalidArgumentException(\sprintf('Serializer %s must implement %s', $serializer, SerializerInterface::class));
        }

        $this->serializer = ObjectInitializer::initialize($serializer, $serializerConstructorArgs);
        $this->constraints = $constraints;

        $this->canSerialize();
    }

    /**
     * @param bool $asEnum If true, returns the name as a SettingName enum; otherwise, returns the name as a string
     *
     * @return string|SettingName The name of the setting, either as a string or as a SettingName enum, depending on the value of $asEnum
     */
    public function getName(bool $asEnum = false): string|SettingName
    {
        return $asEnum ? $this->name : $this->name->value;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): static
    {
        $this->value = $value;
        $this->canSerialize();

        return $this;
    }

    public function getSerializedValue(): string
    {
        return $this->serializer->serialize($this->value);
    }

    private function canSerialize(): void
    {
        $this->serializer->canSerialize($this->value);

        $validator = Validation::createValidator();
        $errors = $validator->validate($this->value, $this->constraints);

        if (\count($errors) > 0) {
            throw new \InvalidArgumentException($errors[0]->getMessage());
        }
    }
}
