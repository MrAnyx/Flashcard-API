<?php

declare(strict_types=1);

namespace App\Setting;

use App\Enum\SettingName;
use App\Setting\Type\BooleanType;
use App\Setting\Type\FloatType;
use App\Setting\Type\IntegerType;
use App\Setting\Type\SettingTypeInterface;
use App\Setting\Type\StringType;
use Symfony\Component\Validator\Constraint;

class SettingEntry
{
    private readonly SettingName $name;

    private readonly mixed $value;

    private readonly SettingTypeInterface $type;

    /**
     * @var Constraint[]
     */
    private readonly array $constraints;

    private readonly array $options;

    /**
     * @param Constraint[] $constraints
     */
    public function __construct(SettingName $name, mixed $value, ?string $type = null, array $constraints = [], array $options = [])
    {
        $this->name = $name;
        $this->value = $value;
        $this->options = $options;
        $this->constraints = $constraints;

        if (!is_a($type, SettingTypeInterface::class, true) && $type !== null) {
            throw new \InvalidArgumentException(\sprintf('The type %s must implement SettingTypeInterface', $type));
        }

        $this->type = new $type ?? match (\gettype($value)) {
            'boolean' => new BooleanType(),
            'integer' => new IntegerType(),
            'double' => new FloatType(),
            'string' => new StringType(),
            default => throw new \InvalidArgumentException(\sprintf('Value with type %s can not be infered', \gettype($this->value)))
        };
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

    public function getSerializedValue(): string
    {
        return $this->type->serialize($this->value, $this->options);
    }

    public function getType(): SettingTypeInterface
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
