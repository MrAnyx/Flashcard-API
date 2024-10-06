<?php

declare(strict_types=1);

namespace App\Attribut;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Virtual
{
    public function __construct(
        public readonly string $hydrateFrom,
    ) {
    }
}
