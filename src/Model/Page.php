<?php

namespace App\Model;

class Page
{
    public function __construct(
        public readonly int $page,
        public readonly string $sort,
        public readonly string $order
    ) {
    }
}
