<?php

declare(strict_types=1);

namespace App\Modifier\Transformer;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.modifier.transformer')]
interface TransformerInterface
{
    public function transform(mixed $rawValue, array $context): mixed;
}
