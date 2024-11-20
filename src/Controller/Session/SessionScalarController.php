<?php

declare(strict_types=1);

namespace App\Controller\Session;

use App\Attribute\RelativeToEntity;
use App\Controller\AbstractRestController;
use App\Entity\Session;
use App\Entity\User;
use App\Enum\CountCriteria\SessionCountCriteria;
use App\Repository\SessionRepository;
use App\Service\PeriodService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
#[RelativeToEntity(Session::class)]
class SessionScalarController extends AbstractRestController
{
    #[Route('/sessions/count/{criteria}', name: 'sessions_count', methods: ['GET'])]
    public function countSessions(
        SessionRepository $sessionRepository,
        Request $request,
        PeriodService $periodService,
        #[CurrentUser] User $user,
        SessionCountCriteria $criteria = SessionCountCriteria::ALL,
    ) {
        $periodType = $this->getPeriodParameter($request);

        $period = $periodService->getDateTimePeriod($periodType);

        $count = match ($criteria) {
            SessionCountCriteria::ALL => $sessionRepository->countAll($user, $period),
            SessionCountCriteria::GROUP_BY_DATE => $sessionRepository->countAllByDate($user, $period),
        };

        return $this->json($count);
    }
}
