<?php

declare(strict_types=1);

namespace App\Modifier;

class Modifier
{
    public function __construct(
        public readonly string $field,
        public readonly string $modifierClassname,
        public readonly array $context = [],
    ) {
    }
}
