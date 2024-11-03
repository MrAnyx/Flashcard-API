<?php

declare(strict_types=1);

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Resource
{
    public function __construct(
        public readonly string $voterAttribute,
    ) {
    }
}
