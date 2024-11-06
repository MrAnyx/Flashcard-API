<?php

declare(strict_types=1);

namespace App\Model;

class Pagination
{
    public function __construct(
        public readonly int $total,
        public readonly int $itemsPerPage,
        public readonly int $count,
        public readonly int $totalpages,
        public readonly int $page,
        public readonly int $offset,
    ) {
    }

    public function hasNextPage(): bool
    {
        return $this->page >= 1 && $this->page < $this->totalpages;
    }

    public function hasPreviousPage(): bool
    {
        return $this->page > 1 && $this->page <= $this->totalpages;
    }
}
