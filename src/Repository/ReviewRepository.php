<?php

namespace App\Repository;

use App\Entity\Unit;
use App\Entity\User;
use App\Entity\Topic;
use App\Entity\Review;
use App\Entity\Flashcard;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Review>
 *
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[] findAll()
 * @method Review[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function resetAllReview(User $user)
    {
        $query = $this->createQueryBuilder('r')
            ->update()
            ->set('r.reset', true)
            ->where('r.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    public function resetBy(Flashcard|Unit|Topic $resetBy, User $user)
    {
        // On met des 2 pour les alias car sinon, il y a des conflits avec la requête principale
        $reviewsToUpdate = $this->createQueryBuilder('r2')
            ->select('r2.id')
            ->join('r2.flashcard', 'f2')
            ->join('f2.unit', 'u2');

        if ($resetBy instanceof Flashcard) {
            $reviewsToUpdate->where('f2 = :resetBy AND r2.user = :user');
        } elseif ($resetBy instanceof Unit) {
            $reviewsToUpdate->where('u2 = :resetBy AND r2.user = :user');
        } elseif ($resetBy instanceof Topic) {
            $reviewsToUpdate->where('u2.topic = :resetBy AND r2.user = :user');
        }

        $reviewsToUpdateDQL = $reviewsToUpdate->getDQL();

        $qb = $this->createQueryBuilder('r');

        return $qb->update()
            ->set('r.reset', true)
            ->where($qb->expr()->in('r.id', $reviewsToUpdateDQL))
            ->setParameter('resetBy', $resetBy)
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
