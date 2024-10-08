<?php

declare(strict_types=1);

namespace App\Model;

class Filter
{
    public function __construct(
        public readonly ?string $filter = null,
        public readonly mixed $value = null,
    ) {
    }

    public function getDoctrineOperator(): string
    {
        if (\is_string($this->value)) {
            return 'LIKE';
        }

        return '=';
    }

    public function getDoctrineParameter(): mixed
    {
        if (\is_string($this->value)) {
            return "%{$this->value}%";
        }

        return $this->value;
    }

    public function isFullyConfigured()
    {
        return $this->filter !== null && $this->value !== null;
    }
}
