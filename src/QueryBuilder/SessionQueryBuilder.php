<?php

declare(strict_types=1);

namespace App\QueryBuilder;

use App\Entity\Session;
use App\QueryFlag\SessionQueryFlag;
use Doctrine\ORM\EntityManagerInterface;

class SessionQueryBuilder extends AbstractQueryBuilder
{
    // public function __construct(
    //     private readonly EntityManagerInterface $em,
    // ) {
    //     parent::__construct(Session::class);
    // }

    // /**
    //  * @param SessionQueryFlag[] $queryFlags
    //  */
    // public function buildInitialQuery(array $queryFlags): void
    // {
    //     // Initial query builder with default selection of session
    //     $query = $this->em
    //         ->getRepository($this->entityFqcn)
    //         ->createQueryBuilder('s')
    //         ->select(\sprintf('s as %s', $this->getEntityNameToCamelCase()));

    //     foreach ($queryFlags as $queryFlag) {
    //         if ($queryFlag === SessionQueryFlag::INCLUDE_TOTAL_REVIEWS) {
    //             $query
    //                 ->addSelect(\sprintf('COUNT(r.id) as %s', Session::TOTAL_REVIEWS_FIELD))
    //                 ->leftJoin('s.reviews', 'r')
    //                 ->addGroupBy('s.id');
    //         }
    //     }

    //     return $query;
    // }
}
