<?php

namespace App\Repository;

use App\Entity\Unit;
use App\Entity\User;
use App\Entity\Topic;
use App\Model\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    public function findAllWithPagination(int $page, string $sort, string $order, User $user = null): Paginator
    {
        $query = $this->createQueryBuilder('u');

        if ($user !== null) {
            $query
                ->join('u.topic', 't')
                ->where('t.author = :user')
                ->setParameter('user', $user);
        }

        $query->orderBy("u.$sort", $order);

        return new Paginator($query, $page);
    }

    public function findByTopicWithPagination(int $page, string $sort, string $order, Topic $topic): Paginator
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.topic = :topic')
            ->setParameter('topic', $topic)
            ->orderBy("u.$sort", $order);

        return new Paginator($query, $page);
    }
}
