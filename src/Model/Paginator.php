<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class Paginator extends DoctrinePaginator
{
    public readonly array $data;

    public readonly Pagination $pagination;

    public function __construct(QueryBuilder|Query $query, Page $pagination, string|int $hydrationMode = AbstractQuery::HYDRATE_OBJECT, bool $fetchJoinCollection = true)
    {
        $offset = ($pagination->page - 1) * $pagination->itemsPerPage;

        $query->setFirstResult($offset);
        $query->setMaxResults($pagination->itemsPerPage);

        parent::__construct($query, $fetchJoinCollection);
        $this->data = $query->getQuery()->getResult($hydrationMode);
        $total = $this->count();

        $this->pagination = new Pagination(
            total: $total,
            itemsPerPage: $pagination->itemsPerPage,
            count: \count($this->data),
            totalpages: (int) ceil($total / $pagination->itemsPerPage),
            page: $pagination->page,
            offset: $offset
        );
    }

    public function hasNextPage(): bool
    {
        return $this->pagination->page >= 1 && $this->pagination->page < $this->pagination->totalpages;
    }

    public function hasPreviousPage(): bool
    {
        return $this->pagination->page > 1 && $this->pagination->page <= $this->pagination->totalpages;
    }
}
