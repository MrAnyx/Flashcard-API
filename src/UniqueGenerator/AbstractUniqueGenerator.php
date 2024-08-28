<?php

declare(strict_types=1);

namespace App\UniqueGenerator;

use App\Exception\MaxTriesReachedException;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractUniqueGenerator
{
    public const MAX_TRIES = 50;

    public function __construct(
        protected EntityManagerInterface $em
    ) {
    }

    public function generate(string $entityFqcn, string $field, int $maxTries = self::MAX_TRIES): mixed
    {
        if (!property_exists($entityFqcn, $field)) {
            throw new \InvalidArgumentException("Field {$field} doesn't exist in entity {$entityFqcn}");
        }

        $repository = $this->em->getRepository($entityFqcn);

        $tries = 0;
        $alreadyExist = true;
        $value = null;

        while ($alreadyExist && $tries < $maxTries) {
            $value = $this->generateValue($tries);

            $alreadyExist = $repository->findOneBy([$field => $value]) !== null;
            ++$tries;
        }

        if ($alreadyExist && $tries >= $maxTries) {
            throw new MaxTriesReachedException("The maximum number of {$maxTries} tries has been reached. Unable to find a unique combinaison for entity {$entityFqcn} and field {$field}.");
        }

        return $value;
    }

    abstract protected function generateValue(int $iteration): mixed;
}
