<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Topic;
use App\Entity\User;
use App\Model\Filter;
use App\Model\Page;
use App\Model\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Topic>
 *
 * @method Topic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Topic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Topic[] findAll()
 * @method Topic[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    public function paginateAndFilterAll(Page $page, ?Filter $filter, ?User $user = null): Paginator
    {
        $query = $this->createQueryBuilder('t');

        if ($user !== null) {
            $query
                ->where('t.author = :user')
                ->setParameter('user', $user);
        }

        if ($filter !== null) {
            $query
                ->andWhere("t.{$filter->filter} {$filter->operator->getDoctrineNotation()} :query")
                ->setParameter('query', $filter->getDoctrineParameter());
        }

        $query
            ->addOrderBy("CASE WHEN t.{$page->sort} IS NULL THEN 1 ELSE 0 END", 'ASC') // To put null values last
            ->addOrderBy("t.{$page->sort}", $page->order->value);

        return new Paginator($query, $page);
    }

    public function findRecentTopics(User $user, int $maxResults = 4): array
    {
        return $this->createQueryBuilder('t')
            ->select('t', 'COUNT(r.id) AS HIDDEN nbReviews')
            ->join('t.units', 'u')
            ->join('u.flashcards', 'f')
            ->join('f.reviewHistory', 'r')
            ->where('t.author = :user')
            ->andWhere('r.reset = :reset')
            ->groupBy('t.id', 'r.date')
            ->orderBy('DATE(r.date)', 'DESC')
            ->addOrderBy('nbReviews', 'DESC')
            ->addOrderBy('r.date', 'DESC')
            ->setMaxResults($maxResults)
            ->setParameter('user', $user)
            ->setParameter('reset', false)
            ->getQuery()
            ->getResult();
    }

    public function countAll(?User $user): int
    {
        $query = $this->createQueryBuilder('t')
            ->select('count(t.id)');

        if ($user !== null) {
            $query
                ->where('t.author = :user')
                ->setParameter('user', $user);
        }

        return $query->getQuery()->getSingleScalarResult();
    }
}
