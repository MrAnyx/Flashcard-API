<?php

declare(strict_types=1);

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Virtual
{
    public function __construct(
        public readonly string $hydrateFrom,
    ) {
    }
}
