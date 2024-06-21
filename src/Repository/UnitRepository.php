<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Topic;
use App\Entity\Unit;
use App\Entity\User;
use App\Model\Page;
use App\Model\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Unit>
 *
 * @method Unit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Unit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Unit[] findAll()
 * @method Unit[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Unit::class);
    }

    public function findAllWithPagination(Page $page, ?User $user = null): Paginator
    {
        $query = $this->createQueryBuilder('u');

        if ($user !== null) {
            $query
                ->join('u.topic', 't')
                ->where('t.author = :user')
                ->setParameter('user', $user);
        }

        $query->orderBy("u.{$page->sort}", $page->order);

        return new Paginator($query, $page);
    }

    public function findByTopicWithPagination(Page $page, Topic $topic): Paginator
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.topic = :topic')
            ->setParameter('topic', $topic)
            ->orderBy("u.{$page->sort}", $page->order);

        return new Paginator($query, $page);
    }

    public function findRecentUnitsByTopic(User $user, ?Topic $topic, int $maxResults = 4): array
    {
        $query = $this->createQueryBuilder('u')
            ->select('u', 'COUNT(r.id) AS HIDDEN nbReviews')
            ->join('u.topic', 't')
            ->join('u.flashcards', 'f')
            ->join('f.reviewHistory', 'r')
            ->where('t.author = :user')
            ->andWhere('r.reset = :reset')
            ->groupBy('u.id')
            ->orderBy('DATE(r.date)', 'DESC')
            ->addOrderBy('nbReviews', 'DESC')
            ->addOrderBy('r.date', 'DESC')
            ->setMaxResults($maxResults)
            ->setParameter('user', $user)
            ->setParameter('reset', false);

        if ($topic !== null) {
            $query
                ->andWhere('t = :topic')
                ->setParameter('topic', $topic);
        }

        return $query->getQuery()->getResult();
    }
}
