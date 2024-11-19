<?php

declare(strict_types=1);

namespace App\Modifier\Mutator;

interface MutatorInterface
{
    public function mutate(object &$entity, mixed $rawValue, array $context): void;
}
