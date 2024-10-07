<?php

declare(strict_types=1);

namespace App\Model;

class Filter
{
    public function __construct(
        public readonly string $field,
        public readonly mixed $value,
    ) {
    }
}
