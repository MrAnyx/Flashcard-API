<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use App\Entity\Session;
use App\Entity\User;
use App\Repository\SessionRepository;
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
    public function getAllSessions(
        Request $request,
        SessionRepository $sessionRepository,
    ): JsonResponse {
        $pagination = $this->getPaginationParameter(Session::class, $request);
        $filter = $this->getFilterParameter(Session::class, $request);

        dd($filter, $pagination);

        /** @var User $user */
        $user = $this->getUser();

        $sessions = $sessionRepository->findAllWithPagination($pagination, $user);

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
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        $count = $sessionRepository->countAll($user);

        return $this->jsonStd($count);
    }
}
