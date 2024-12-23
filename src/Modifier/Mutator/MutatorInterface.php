<?php

declare(strict_types=1);

namespace App\Modifier\Mutator;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.modifier.mutator')]
interface MutatorInterface
{
    public function mutate(object &$entity, mixed $rawValue, array $context): void;
}
