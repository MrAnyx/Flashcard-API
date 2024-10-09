<?php

declare(strict_types=1);

namespace App\Serializer;

class DateTimeSerializer implements SerializerInterface
{
    public function __construct(private string $format = \DateTimeImmutable::ATOM)
    {
    }

    public function canSerialize(mixed $value): bool
    {
        return $value instanceof \DateTimeInterface;
    }

    public function serialize(mixed $value): string
    {
        if (!$this->canSerialize($value)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid datetime.', $value));
        }

        return $value->format($this->format);
    }

    public function canDeserialize(string $value): bool
    {
        return \DateTimeImmutable::createFromFormat($this->format, $value) !== false;
    }

    public function deserialize(string $value): mixed
    {
        if (!$this->canDeserialize($value)) {
            throw new \InvalidArgumentException(\sprintf('"%s" must match the format %s.', $value, $this->format));
        }

        return \DateTimeImmutable::createFromFormat($this->format, $value);
    }
}
