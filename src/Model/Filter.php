<?php

declare(strict_types=1);

namespace App\Model;

use App\Enum\OperatorType;

class Filter
{
    public function __construct(
        public readonly ?string $filter = null,
        public readonly mixed $value = null,
        public readonly OperatorType $operator = OperatorType::EQUAL,
    ) {
    }

    public function getDoctrineParameter(): mixed
    {
        return match ($this->operator) {
            OperatorType::LIKE => "%{$this->value}%",
            default => $this->value,
        };
    }

    public function isFullyConfigured()
    {
        return $this->filter !== null && $this->value !== null && $this->operator !== null;
    }
}
