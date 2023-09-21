<?php

namespace App\Repository;

use App\Entity\Unit;
use App\Entity\User;
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

    //    /**
    //     * @return Unit[] Returns an array of Unit objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Unit
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
