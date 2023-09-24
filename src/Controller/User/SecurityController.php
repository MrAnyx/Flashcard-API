<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Exception\ApiException;
use App\Controller\AbstractRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/auth', 'api_auth_', format: 'json')]
class SecurityController extends AbstractRestController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        /** @var ?User $user */
        $user = $this->getUser();

        if ($user === null) {
            throw new ApiException(Response::HTTP_UNAUTHORIZED, 'Missing credentials');
        }

        return $this->json([
            'token' => $user->getToken(),
        ]);
    }
}
