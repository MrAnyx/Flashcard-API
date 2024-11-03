<?php

declare(strict_types=1);

namespace App\Attribute;

use App\ValueResolver\BodyResolver;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Body extends ValueResolver
{
    public function __construct()
    {
        parent::__construct(BodyResolver::class);
    }
}
