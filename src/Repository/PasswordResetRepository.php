<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PasswordReset;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PasswordReset>
 */
class PasswordResetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordReset::class);
    }

    public function getLastRequest(User $user): ?PasswordReset
    {
        return $this->createQueryBuilder('pr')
            ->andWhere('pr.user = :user')
            ->andWhere('pr.expirationDate > :now')
            ->andWhere('pr.used = :used')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('used', false)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByToken(string $rawToken): ?PasswordReset
    {
        return $this->createQueryBuilder('pr')
            ->andWhere('pr.expirationDate > :now')
            ->andWhere('pr.used = :used')
            ->andWhere('pr.token = :token')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('used', false)
            ->setParameter('token', hash(PasswordReset::TOKEN_HASH_ALGO, $rawToken))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
