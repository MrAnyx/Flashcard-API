<?php

declare(strict_types=1);

namespace App\Model;

readonly class TypedField
{
    public function __construct(
        public string $name,
        public \ReflectionType $type,
    ) {
    }
}
