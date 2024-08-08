<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Flashcard;
use App\Entity\Review;
use App\Entity\Topic;
use App\Entity\Unit;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function resetBy(User $user, Flashcard|Unit|Topic|null $resetBy = null)
    {
        // On met des 2 pour les alias car sinon, il y a des conflits avec la requête principale
        $reviewsToUpdate = $this->createQueryBuilder('r2')
            ->select('r2.id')
            ->join('r2.flashcard', 'f2')
            ->join('f2.unit', 'u2')
            ->where('r2.user = :user');

        if ($resetBy instanceof Flashcard) {
            $reviewsToUpdate->andWhere('f2 = :resetBy');
        } elseif ($resetBy instanceof Unit) {
            $reviewsToUpdate->andWhere('u2 = :resetBy');
        } elseif ($resetBy instanceof Topic) {
            $reviewsToUpdate->andWhere('u2.topic = :resetBy');
        }

        $qb = $this->createQueryBuilder('r');

        $qb
            ->update()
            ->set('r.reset', true)
            ->andWhere($qb->expr()->in('r.id', $reviewsToUpdate->getDQL()))
            ->setParameter('user', $user);

        if ($resetBy !== null) {
            $qb->setParameter('resetBy', $resetBy);
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    public function countReviews(User $user, bool $withReset = false)
    {
        $query = $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->where('r.user = :user')
            ->setParameter('user', $user);

        if (!$withReset) {
            $query
                ->andWhere('r.reset = :withReset')
                ->setParameter('withReset', false);
        }

        return $query->getQuery()->getSingleScalarResult();
    }
}
