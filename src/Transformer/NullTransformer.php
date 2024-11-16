<?php

declare(strict_types=1);

namespace App\Transformer;

class NullTransformer implements TransformerInterface
{
    public function transform(mixed $rawValue, array $context): mixed
    {
        return null;
    }
}
