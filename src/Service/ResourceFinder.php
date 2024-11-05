<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ResourceFinder
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
    ) {
    }

    /**
     * @template T
     *
     * @param class-string<T> $classname
     *
     * @return T
     */
    public function getResourceById(string $classname, mixed $id, bool $allowNull = false, ?string $voterAttribute = null): object
    {
        $resource = $this->em->find($classname, $id);

        if ($resource === null && $allowNull) {
            throw new NotFoundException(\sprintf('Resource of type %s with id %s was not found', $classname, $id));
        }

        if ($voterAttribute && !$this->security->isGranted($voterAttribute, $resource)) {
            throw new UnauthorizedException('You cannot access this resource');
        }

        return $resource;
    }
}
