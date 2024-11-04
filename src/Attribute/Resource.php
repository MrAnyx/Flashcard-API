<?php

declare(strict_types=1);

namespace App\Attribute;

use App\ValueResolver\ResourceByIdResolver;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Resource extends ValueResolver
{
    public function __construct(
        public readonly ?string $voterAttribute = null,
        public readonly string $idUrlSegment = 'id',
    ) {
        parent::__construct(ResourceByIdResolver::class);
    }
}
