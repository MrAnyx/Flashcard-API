<?php

declare(strict_types=1);

namespace App\Setting;

use App\Enum\SettingName;
use App\Serializer\SerializerInterface;
use App\Setting\Type\AbstractSettingType;
use Symfony\Component\Validator\Constraint;

class SettingEntry
{
    /**
     * @var Constraint[]
     */
    private readonly array $constraints;

    private readonly SettingName $name;

    private mixed $value;

    private readonly SerializerInterface $type;

    private readonly array $options;

    /**
     * @param Constraint[] $constraints
     */
    public function __construct(SettingName $name, mixed $value, string $settingTypeFqcn, array $constraints = [], array $options = [])
    {
        $this->name = $name;
        $this->value = $value;
        $this->options = $options;

        if (!is_a($settingTypeFqcn, AbstractSettingType::class, true)) {
            throw new \InvalidArgumentException(\sprintf('The type %s must implement AbstractSettingType', $settingTypeFqcn));
        }

        $this->type = new $settingTypeFqcn();
        $this->constraints = $constraints;

        $this->type->canSerialize($this->value, $this->constraints);
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
        $this->type->canSerialize($this->value, $this->constraints);

        return $this;
    }

    public function getSerializedValue(): string
    {
        return $this->type->serialize($this->value, $this->options);
    }

    public function getType(): SerializerInterface
    {
        return $this->type;
    }

    /**
     * @return Constraint[]
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
