<?php

declare(strict_types=1);

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Searchable
{
    public function __construct(
        public readonly ?string $converterFqcn = null,
        public readonly array $options = [],
    ) {
    }
}
