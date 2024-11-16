<?php

declare(strict_types=1);

namespace App\Transformer;

class Transformer
{
    public function __construct(
        public readonly string $transformerClassname,
        public readonly array $context = [],
    ) {
    }
}
