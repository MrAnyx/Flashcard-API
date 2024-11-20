<?php

declare(strict_types=1);

namespace App\Controller\Session;

use App\Attribute\RelativeToEntity;
use App\Attribute\Resource;
use App\Controller\AbstractRestController;
use App\Entity\Session;
use App\Utility\Regex;
use App\Voter\SessionVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', 'api_', format: 'json')]
#[RelativeToEntity(Session::class)]
class SessionBehaviorController extends AbstractRestController
{
    #[Route('/sessions/{id}/stop', name: 'session_stop', methods: ['POST'], requirements: ['id' => Regex::INTEGER])]
    public function stopSession(
        EntityManagerInterface $em,
        #[Resource(SessionVoter::OWNER)] Session $session,
    ): JsonResponse {
        $session->setEndedAt(new \DateTimeImmutable());
        $em->flush();

        return $this->json($session, context: ['groups' => ['read:session:user']]);
    }
}
