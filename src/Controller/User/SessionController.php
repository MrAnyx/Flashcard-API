<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use App\Entity\Session;
use App\Entity\User;
use App\Enum\CountCriteria\SessionCountCriteria;
use App\Repository\SessionRepository;
use App\Service\PeriodService;
use App\Utility\Regex;
use App\Voter\SessionVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', 'api_', format: 'json')]
class SessionController extends AbstractRestController
{
    #[Route('/sessions', name: 'get_sessions', methods: ['GET'])]
    public function getSessions(
        Request $request,
        SessionRepository $sessionRepository,
    ): JsonResponse {
        $pagination = $this->getPaginationParameter(Session::class, $request);
        $filter = $this->getFilterParameter(Session::class, $request);

        /** @var User $user */
        $user = $this->getUser();

        $sessions = $sessionRepository->paginateAndFilterAll($pagination, $filter, $user);

        return $this->jsonStd($sessions, context: ['groups' => ['read:session:user']]);
    }

    #[Route('/sessions/{id}/stop', name: 'session_stop', methods: ['POST'], requirements: ['id' => Regex::INTEGER])]
    public function stopSession(
        int $id,
        EntityManagerInterface $em,
    ): JsonResponse {
        $session = $this->getResourceById(Session::class, $id);
        $this->denyAccessUnlessGranted(SessionVoter::OWNER, $session, 'You can not update this resource');

        $session->setEndedAt(new \DateTimeImmutable());
        $em->flush();

        return $this->jsonStd($session, context: ['groups' => ['read:session:user']]);
    }

    #[Route('/sessions/count', name: 'sessions_count', methods: ['GET'])]
    public function countSessions(
        SessionRepository $sessionRepository,
        Request $request,
        PeriodService $periodService,
    ) {
        $criteria = $this->getCountCriteria($request, SessionCountCriteria::class, SessionCountCriteria::ALL->value);
        $periodType = $this->getPeriodParameter($request);

        $period = $periodService->getDateTimePeriod($periodType);

        /** @var User $user */
        $user = $this->getUser();

        $count = match ($criteria) {
            SessionCountCriteria::ALL => $sessionRepository->countAll($user, $period),
            SessionCountCriteria::GROUP_BY_DATE => $sessionRepository->countAllByDate($user, $period),
        };

        return $this->jsonStd($count);
    }
}
