<?php

namespace App\Repository;

use App\Entity\User;
use App\Model\Paginator;
use App\Entity\Flashcard;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Flashcard>
 *
 * @method Flashcard|null find($id, $lockMode = null, $lockVersion = null)
 * @method Flashcard|null findOneBy(array $criteria, array $orderBy = null)
 * @method Flashcard[] findAll()
 * @method Flashcard[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlashcardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flashcard::class);
    }

    public function findAllWithPagination(int $page, string $sort, string $order, ?User $user): Paginator
    {
        $query = $this->createQueryBuilder('f');

        if ($user !== null) {
            $query
                ->where('f.author = :user')
                ->setParameter('user', $user);
        }

        $query->orderBy("f.$sort", $order);

        return new Paginator($query, $page);
    }

    //    /**
    //     * @return Flashcard[] Returns an array of Flashcard objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Flashcard
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
