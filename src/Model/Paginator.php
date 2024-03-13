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
        if ($this->getCurrentPage() >= 1 && $this->getCurrentPage() < $this->getTotalPages()) {
            return true;
        }

        return false;
    }

    public function hasPreviousPage(): bool
    {
        if ($this->getCurrentPage() > 1 && $this->getCurrentPage() <= $this->getTotalPages()) {
            return true;
        }

        return false;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator([
            'data' => $this->getData(),
            'pagination' => [
                'total' => $this->getTotal(),
                'count' => $this->getCount(),
                'offset' => $this->getOffset(),
                'items_per_page' => $this->getItemsPerPage(),
                'total_pages' => $this->getTotalPages(),
                'current_page' => $this->getCurrentPage(),
                'has_next_page' => $this->hasNextPage(),
                'has_previous_page' => $this->hasPreviousPage(),
            ],
        ]);
    }
}
