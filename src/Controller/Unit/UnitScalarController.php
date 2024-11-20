<?php

declare(strict_types=1);

namespace App\Controller\Unit;

use App\Attribute\RelativeToEntity;
use App\Controller\AbstractRestController;
use App\Entity\Unit;
use App\Entity\User;
use App\Enum\CountCriteria\UnitCountCriteria;
use App\Repository\UnitRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
#[RelativeToEntity(Unit::class)]
class UnitScalarController extends AbstractRestController
{
    #[Route('/units/count/{criteria}', name: 'unit_count', methods: ['GET'])]
    public function countUnits(
        UnitRepository $unitRepository,
        #[CurrentUser] User $user,
        UnitCountCriteria $criteria = UnitCountCriteria::ALL,
    ): JsonResponse {
        $count = match ($criteria) {
            UnitCountCriteria::ALL => $unitRepository->countAll($user),
        };

        return $this->json($count);
    }
}
