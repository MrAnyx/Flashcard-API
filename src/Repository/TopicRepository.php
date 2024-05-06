<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Review;
use App\Entity\Topic;
use App\Entity\User;
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

    public function findAllWithPagination(Page $page, ?User $user = null): Paginator
    {
        $query = $this->createQueryBuilder('t');

        if ($user !== null) {
            $query
                ->where('t.author = :user')
                ->setParameter('user', $user);
        }

        $query->orderBy("t.{$page->sort}", $page->order);

        return new Paginator($query, $page->page);
    }

    public function findRecentTopics(User $user, int $maxResults = 5): array
    {
        $reviewRepository = $this->getEntityManager()->getRepository(Review::class);

        $query = $reviewRepository->createQueryBuilder('r2')
            ->select('t2.id')
            ->join('r2.flashcard', 'f2')
            ->join('f2.unit', 'u2')
            ->join('u2.topic', 't2')
            ->where('t2.author = :user')
            ->andWhere('r2.reset = :reset')
            ->groupBy('t2.id')
            ->setMaxResults($maxResults)
            ->setParameter('user', $user)
            ->setParameter('reset', false)
            ->getQuery()
            ->getResult();

        return $this->createQueryBuilder('t')
            ->andWhere('t.id IN (:ids)')
            ->setParameter('ids', $query)
            ->getQuery()
            ->getResult();
    }
}
