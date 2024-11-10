<?php

declare(strict_types=1);

namespace App\Controller\Unit;

use App\Controller\AbstractRestController;
use App\Entity\User;
use App\Enum\CountCriteria\UnitCountCriteria;
use App\Repository\UnitRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
class UnitScalarController extends AbstractRestController
{
    #[Route('/units/count', name: 'unit_count', methods: ['GET'])]
    public function countUnits(
        UnitRepository $unitRepository,
        Request $request,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $criteria = $this->getCountCriteria($request, UnitCountCriteria::class, UnitCountCriteria::ALL->value);

        $count = match ($criteria) {
            UnitCountCriteria::ALL => $unitRepository->countAll($user),
        };

        return $this->jsonStd($count);
    }
}
