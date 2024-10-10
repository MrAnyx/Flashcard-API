<?php

declare(strict_types=1);

namespace App\Serializer;

class DateTimeSerializer implements SerializerInterface
{
    public function __construct(private string $format = \DateTimeImmutable::ATOM)
    {
    }

    public function canSerialize(mixed $value): void
    {
        if (!$value instanceof \DateTimeInterface) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid datetime.', $value));
        }
    }

    public function serialize(mixed $value): string
    {
        $this->canSerialize($value);

        return $value->format($this->format);
    }

    public function canDeserialize(string $value): void
    {
        if (\DateTimeImmutable::createFromFormat($this->format, $value) === false) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid datetime.', $value));
        }
    }

    public function deserialize(string $value): mixed
    {
        $this->canDeserialize($value);

        return \DateTimeImmutable::createFromFormat($this->format, $value);
    }
}
