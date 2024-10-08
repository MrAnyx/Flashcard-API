<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Session;
use App\Entity\User;
use App\Hydrator\VirtualHydrator;
use App\Model\Filter;
use App\Model\Page;
use App\Model\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SessionRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Session::class);
    }

    public function paginateAndFilterAll(Page $page, Filter $filter, ?User $user = null)
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

        if ($filter->isFullyConfigured()) {
            $query
                ->andWhere("s.{$filter->filter} {$filter->operator->getDoctrineNotation()} :query")
                ->setParameter('query', $filter->getDoctrineParameter());
        }

        $query
            ->addOrderBy("CASE WHEN s.{$page->sort} IS NULL THEN 1 ELSE 0 END", 'ASC') // To put null values last
            ->addOrderBy("s.{$page->sort}", $page->order);

        return new Paginator($query, $page, VirtualHydrator::class);
    }

    public function countAll(?User $user): int
    {
        $query = $this->createQueryBuilder('s')
            ->select('count(s.id)');

        if ($user !== null) {
            $query
                ->where('s.author = :user')
                ->setParameter('user', $user);
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @return array{
     *     current: int,
     *     longest: int
     * }
     */
    public function getStrike(User $user): array
    {
        // dates are sorted from latest to oldest
        $rawDates = $this->createQueryBuilder('s')
            ->select('DATE(s.started_at)')
            ->distinct()
            ->where('s.author = :user')
            ->orderBy('DATE(s.started_at)', 'DESC')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleColumnResult();

        if (\count($rawDates) === 0) {
            return [
                'current' => 0,
                'longest' => 0,
            ];
        }

        $dates = array_map(fn ($date) => new \DateTimeImmutable($date), $rawDates);

        /*
        [
            2024-08-17
            2024-08-16
            2024-08-15
            2024-08-10 // break
            2024-08-02 // break
        ]
        */

        $longestStreak = 0;
        $currentStreak = 0;

        // $today = new \DateTime('today');
        // $yesterday = (clone $today)->modify('-1 day');
        // $startIndex = 0;

        // if ($dates[0]->format('Y-m-d') === $today->format('Y-m-d')) {
        //     ++$currentStreak;
        //     $startIndex = 1;
        // }

        // if ($dates[0]->format('Y-m-d') === $yesterday->format('Y-m-d')) {
        //     ++$currentStreak;
        // }

        // $comparisonDate = $yesterday;

        // for ($i = $startIndex; $i < \count($dates); ++$i) {
        //     if ($dates[$i]->format('Y-m-d') === $comparisonDate->format('Y-m-d')) {
        //         ++$currentStreak;
        //         $comparisonDate->modify('-1 day');
        //     } else {
        //         break;
        //     }
        // }

        return [
            'current' => $currentStreak,
            'longest' => $longestStreak,
        ];
    }
}
