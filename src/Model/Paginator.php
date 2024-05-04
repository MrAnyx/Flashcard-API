<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class Paginator extends DoctrinePaginator
{
    public const ITEMS_PER_PAGE = 25;

    private int $total;

    private array $data;

    private int $count;

    private int $totalpages;

    private int $page;

    public function __construct(QueryBuilder|Query $query, int $page = 1, bool $fetchJoinCollection = true)
    {
        $query->setFirstResult(($page - 1) * self::ITEMS_PER_PAGE);
        $query->setMaxResults(self::ITEMS_PER_PAGE);

        parent::__construct($query, $fetchJoinCollection);
        $this->total = $this->count();
        $this->data = iterator_to_array(parent::getIterator());
        $this->count = \count($this->data);
        $this->page = $page;

        $this->totalpages = (int) ceil($this->total / self::ITEMS_PER_PAGE);
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getTotalPages(): int
    {
        return $this->totalpages;
    }

    public function getCurrentPage(): int
    {
        return $this->page;
    }

    public function getItemsPerPage(): ?int
    {
        return $this->getQuery()->getMaxResults();
    }

    public function getOffset(): int
    {
        return $this->getQuery()->getFirstResult();
    }

    public function hasNextPage(): bool
    {
        return $this->getCurrentPage() >= 1 && $this->getCurrentPage() < $this->getTotalPages();
    }

    public function hasPreviousPage(): bool
    {
        return $this->getCurrentPage() > 1 && $this->getCurrentPage() <= $this->getTotalPages();
    }
}
