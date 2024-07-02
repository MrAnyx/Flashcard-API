<?php

declare(strict_types=1);

namespace App\Model;

readonly class Filter
{
    public function __construct(
        public ?string $filter,
        public mixed $query,
    ) {
    }

    public function isFullyConfigured(): bool
    {
        return $this->filter !== null && $this->query !== null;
    }
}
