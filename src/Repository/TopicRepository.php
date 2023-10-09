<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Topic;
use App\Model\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    public function findAllWithPagination(int $page, string $sort, string $order, User $user = null): Paginator
    {
        $query = $this->createQueryBuilder('t');

        if ($user !== null) {
            $query
                ->where('t.author = :user')
                ->setParameter('user', $user);
        }

        $query->orderBy("t.$sort", $order);

        return new Paginator($query, $page);
    }
}
