<?php

declare(strict_types=1);

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Searchable
{
    public function __construct(
        public readonly ?string $serializerFqcn = null,
        public readonly array $serializerConstructorParams = [],
    ) {
    }
}
