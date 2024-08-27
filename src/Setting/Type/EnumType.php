<?php

declare(strict_types=1);

namespace App\Setting\Type;

class EnumType implements SettingTypeInterface
{
    public function serialize(mixed $value, array $options = []): string
    {
        $class = (string) $options['class'] ?? throw new \LogicException('Missing class option for enum type');

        // Check if class exist
        if (!class_exists($class)) {
            throw new \LogicException(\sprintf('Unknown class %s', \gettype($class)));
        }

        // Check if class is an enum
        if (!is_a($value, \BackedEnum::class, true)) {
            throw new \LogicException(\sprintf('The enum "%s" must be a backed enum', $class));
        }

        // Check if the value is an instance of class
        if (!is_a($value, $class)) {
            throw new \LogicException(\sprintf('Expected type "%s", but "%s" given', $class, \gettype($value)));
        }

        return (string) $value;
    }

    public function deserialize(string $value, array $options = []): mixed
    {
        $class = (string) $options['class'] ?? throw new \LogicException('Missing class option for enum type');

        // Check if class is an enum
        if (!is_a($value, \BackedEnum::class)) {
            throw new \LogicException(\sprintf('The enum "%s" must be a backed enum', $class));
        }

        return $class::from($value);
    }
}
