<?php

declare(strict_types=1);

namespace App\Model;

readonly class Page
{
    public function __construct(
        public int $page,
        public string $sort,
        public string $order,
        public int $itemsPerPage
    ) {
    }
}
