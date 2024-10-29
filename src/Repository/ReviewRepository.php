<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Flashcard;
use App\Entity\Review;
use App\Entity\Session;
use App\Entity\Topic;
use App\Entity\Unit;
use App\Entity\User;
use App\Model\Period;
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
            ->join('u2.topic', 't2')
            ->where('t2.author = :user');

        if ($resetBy instanceof Flashcard) {
            $reviewsToUpdate->andWhere('f2 = :resetBy');
        } elseif ($resetBy instanceof Unit) {
            $reviewsToUpdate->andWhere('u2 = :resetBy');
        } elseif ($resetBy instanceof Topic) {
            $reviewsToUpdate->andWhere('t2 = :resetBy');
        }

        $qb = $this->createQueryBuilder('r');

        $qb
            ->update()
            ->set('r.reset', ':reset')
            ->andWhere($qb->expr()->in('r.id', $reviewsToUpdate->getDQL()))
            ->setParameter('reset', true)
            ->setParameter('user', $user);

        if ($resetBy !== null) {
            $qb->setParameter('resetBy', $resetBy);
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    public function countReviews(User $user, Period $period, bool $withReset)
    {
        $query = $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->join('r.flashcard', 'f')
            ->join('f.unit', 'u')
            ->join('u.topic', 't')
            ->where('t.author = :user')
            ->setParameter('user', $user);

        if (!$withReset) {
            $query
                ->andWhere('r.reset = :withReset')
                ->setParameter('withReset', false);
        }

        $query
            ->andWhere('r.date BETWEEN :start AND :end')
            ->setParameter('start', $period->start)
            ->setParameter('end', $period->end);

        return $query->getQuery()->getSingleScalarResult();
    }

    public function findAllBySession(Session $session, bool $withReset): array
    {
        $query = $this->createQueryBuilder('r')
            ->select('r, f, u, t')
            ->join('r.flashcard', 'f')
            ->join('f.unit', 'u')
            ->join('u.topic', 't')
            ->where('r.session = :session')
            ->setParameter('session', $session)
            ->orderBy('r.date', 'ASC');

        if (!$withReset) {
            $query
                ->andWhere('r.reset = :withReset')
                ->setParameter('withReset', false);
        }

        return $query->getQuery()->getResult();
    }

    public function countAllByDate(User $user, Period $period)
    {
        return $this->createQueryBuilder('r')
            ->select('DATE(r.date) AS date, count(r.id) total')
            ->join('r.session', 's')
            ->where('s.author = :user')
            ->andWhere('r.date BETWEEN :start AND :end')
            ->groupBy('date')
            ->setParameter('user', $user)
            ->setParameter('start', $period->start)
            ->setParameter('end', $period->end)
            ->getQuery()
            ->getResult();
    }
}
