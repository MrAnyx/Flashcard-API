<?php

declare(strict_types=1);

namespace App\Serializer;

class DateTimeSerializer implements SerializerInterface
{
    public function __construct(private string $format = \DateTimeImmutable::ATOM)
    {
    }

    /**
     * @param \DateTimeInterface $value
     */
    public function serialize(mixed $value): string
    {
        if (!$value instanceof \DateTimeInterface) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a valid datetime.', $value));
        }

        return $value->format($this->format);
    }

    public function deserialize(string $value): mixed
    {
        $dateTime = \DateTimeImmutable::createFromFormat($this->format, $value);

        if (!$dateTime) {
            throw new \InvalidArgumentException(\sprintf('"%s" must match the format %s.', $value, $this->format));
        }

        return $dateTime;
    }
}
