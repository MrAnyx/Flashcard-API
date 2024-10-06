<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class Paginator extends DoctrinePaginator
{
    private int $total;

    private array $data;

    private int $count;

    private int $totalpages;

    private int $page;

    private int $itemsPerPage;

    private int $offset;

    public function __construct(QueryBuilder|Query $query, Page $pagination, string|int $hydrationMode = AbstractQuery::HYDRATE_OBJECT, bool $fetchJoinCollection = true)
    {
        $this->itemsPerPage = $pagination->itemsPerPage;
        $this->offset = ($pagination->page - 1) * $this->itemsPerPage;

        $query->setFirstResult($this->offset);
        $query->setMaxResults($this->itemsPerPage);

        parent::__construct($query, $fetchJoinCollection);
        $this->total = $this->count();
        $this->data = $query->getQuery()->getResult($hydrationMode);
        $this->count = \count($this->data);
        $this->page = $pagination->page;

        $this->totalpages = (int) ceil($this->total / $this->itemsPerPage);
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
        return $this->itemsPerPage;
    }

    public function getOffset(): int
    {
        return $this->offset;
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
