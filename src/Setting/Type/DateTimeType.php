<?php

declare(strict_types=1);

namespace App\Setting\Type;

use DateTimeInterface;

class DateTimeType implements SettingTypeInterface
{
    public function serialize(mixed $value, array $options = []): string
    {
        $class = (string) $options['class'] ?? throw new \LogicException('Missing class option for datetime type');

        // Check if class exist
        if (!class_exists($class)) {
            throw new \LogicException(\sprintf('Unknown class %s', \gettype($class)));
        }

        // Check if class implements the DateTimeInterface
        if (!is_a($class, \DateTimeInterface::class, true)) {
            throw new \LogicException(\sprintf('The class %s must be implement a DateTimeInterface', $class));
        }

        // Check if the value is an instance of class
        if (!is_a($value, $class)) {
            throw new \LogicException(\sprintf('Expected type "%s", but "%s" given', $class, \gettype($value)));
        }

        return $value->format(\DateTimeInterface::ATOM);
    }

    public function deserialize(string $value, array $options = []): \DateTimeInterface
    {
        $class = (string) $options['class'] ?? throw new \LogicException('Missing class option for datetime type');

        if (!is_a($class, \DateTime::class, true) && !is_a($class, \DateTimeImmutable::class, true)) {
            throw new \LogicException(\sprintf('The class %s must extend either DateTime or DateTimeImmutable', $class));
        }

        // Create a new instance of the class with the given value
        $datetime = $class::createFromFormat(\DateTimeInterface::ATOM, $value);

        if ($datetime === false) {
            throw new \RuntimeException(\sprintf('Failed to create a new instance of %s', $class));
        }

        return $datetime;
    }
}
