<?php

declare(strict_types=1);

namespace App\Modifier\Transformer;

interface TransformerInterface
{
    public function transform(mixed $rawValue, array $context): mixed;
}
