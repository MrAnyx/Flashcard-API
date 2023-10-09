<?php

namespace App\Repository;

use DateTime;
use App\Entity\Unit;
use App\Entity\User;
use App\Entity\Topic;
use App\Enum\StateType;
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

    public function findAllWithPagination(int $page, string $sort, string $order, User $user = null): Paginator
    {
        $query = $this->createQueryBuilder('f');

        if ($user !== null) {
            $query
                ->join('f.unit', 'u')
                ->join('u.topic', 't')
                ->where('t.author = :user')
                ->setParameter('user', $user);
        }

        $query->orderBy("f.$sort", $order);

        return new Paginator($query, $page);
    }

    public function findByUnitWithPagination(int $page, string $sort, string $order, Unit $unit): Paginator
    {
        $query = $this->createQueryBuilder('f')
            ->where('f.unit = :unit')
            ->setParameter('unit', $unit)
            ->orderBy("f.$sort", $order);

        return new Paginator($query, $page);
    }

    public function resetAll(User $user)
    {
        // On met des 2 pour les alias car sinon, il y a des conflits avec la requête principale
        $flashcardsToReset = $this->createQueryBuilder('f2')
            ->select('f2.id')
            ->join('f2.unit', 'u2')
            ->join('u2.topic', 't2')
            ->where('t2.author = :user')
            ->getDQL();

        $qb = $this->createQueryBuilder('f');

        return $qb->update()
            ->set('f.previousReview', ':previousReview')
            ->set('f.state', ':state')
            ->set('f.nextReview', ':nextReview')
            ->set('f.difficulty', ':difficulty')
            ->set('f.stability', ':stability')
            ->where($qb->expr()->in('f.id', $flashcardsToReset))
            ->setParameter('user', $user)
            ->setParameter('previousReview', null)
            ->setParameter('state', StateType::New)
            ->setParameter('nextReview', null)
            ->setParameter('difficulty', null)
            ->setParameter('stability', null)
            ->getQuery()
            ->execute();
    }

    public function resetBy(Flashcard|Unit|Topic $resetBy, User $user)
    {
        // On met des 2 pour les alias car sinon, il y a des conflits avec la requête principale
        $flashcardsToReset = $this->createQueryBuilder('f2')
            ->select('f2.id')
            ->join('f2.unit', 'u2')
            ->join('u2.topic', 't2');

        if ($resetBy instanceof Flashcard) {
            $flashcardsToReset->where('f2 = :resetBy AND t2.author = :user');
        } elseif ($resetBy instanceof Unit) {
            $flashcardsToReset->where('u2 = :resetBy AND t2.author = :user');
        } elseif ($resetBy instanceof Topic) {
            $flashcardsToReset->where('u2.topic = :resetBy AND t2.author = :user');
        }

        $flashcardsToResetDQL = $flashcardsToReset->getDQL();

        $qb = $this->createQueryBuilder('f');

        return $qb->update()
            ->set('f.previousReview', ':previousReview')
            ->set('f.state', ':state')
            ->set('f.nextReview', ':nextReview')
            ->set('f.difficulty', ':difficulty')
            ->set('f.stability', ':stability')
            ->where($qb->expr()->in('f.id', $flashcardsToResetDQL))
            ->setParameter('resetBy', $resetBy)
            ->setParameter('user', $user)
            ->setParameter('previousReview', null)
            ->setParameter('state', StateType::New)
            ->setParameter('nextReview', null)
            ->setParameter('difficulty', null)
            ->setParameter('stability', null)
            ->getQuery()
            ->execute();
    }

    public function findFlashcardToReview(User $user, int $cardsToReview)
    {
        $result = $this->createQueryBuilder('f')
            ->join('f.unit', 'u')
            ->join('u.topic', 't')
            ->where('(t.author = :user AND f.nextReview <= :today) OR f.nextReview IS NULL')
            ->orderBy('f.nextReview', 'ASC')
            ->setMaxResults($cardsToReview)
            ->setParameter('user', $user)
            ->setParameter('today', (new DateTime())->format('Y-m-d'))
            ->getQuery()
            ->getResult();

        return $result;
    }
}
