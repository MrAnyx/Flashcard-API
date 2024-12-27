<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Session;
use App\Entity\User;
use App\Hydrator\VirtualHydrator;
use App\Model\Filter;
use App\Model\Page;
use App\Model\Paginator;
use App\Model\Period;
use App\Trait\UseRepositoryExtension;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SessionRepository extends ServiceEntityRepository
{
    use UseRepositoryExtension;

    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Session::class);
    }

    public function paginateAndFilterAll(Page $page, ?Filter $filter, ?User $user = null)
    {
        $query = $this->createQueryBuilder('s')
            ->select('s, COUNT(r.id) as totalReviews')
            ->leftJoin('s.reviews', 'r')
            ->addGroupBy('s.id');

        if ($user !== null) {
            $query
                ->where('s.author = :user')
                ->setParameter('user', $user);
        }

        if ($filter !== null) {
            $this->addFilter($query, 's', $filter);
        }

        $this->addSort($query, 's', $page);

        return new Paginator($query, $page, VirtualHydrator::class);
    }

    public function countAll(?User $user, ?Period $period): int
    {
        $query = $this->createQueryBuilder('s')
            ->select('count(s.id)');

        if ($user !== null) {
            $query
                ->where('s.author = :user')
                ->setParameter('user', $user);
        }

        if ($period !== null) {
            $query
                ->andWhere('s.startedAt BETWEEN :start AND :end')
                ->setParameter('start', $period->start)
                ->setParameter('end', $period->end);
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    public function countAllByDate(User $user, ?Period $period)
    {
        $query = $this->createQueryBuilder('s')
            ->select('DATE(s.startedAt) AS date, count(s.id) total')
            ->where('s.author = :user')
            ->groupBy('date')
            ->setParameter('user', $user);

        if ($period !== null) {
            $query
                ->andWhere('s.startedAt BETWEEN :start AND :end')
                ->setParameter('start', $period->start)
                ->setParameter('end', $period->end);
        }

        return $query
            ->getQuery()
            ->getResult();
    }
}
