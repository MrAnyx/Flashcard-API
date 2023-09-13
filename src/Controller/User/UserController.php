<?php

namespace App\Controller\User;

use App\Service\EntityChecker;
use App\Exception\ApiException;
use App\Controller\AbstractRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', 'api_', format: 'json')]
class UserController extends AbstractRestController
{
    #[Route('/users/me', name: 'get_me', methods: ['GET'])]
    public function getMe(EntityChecker $entityChecker)
    {
        $user = $this->getUser();

        if ($user === null) {
            throw new ApiException('You must be logged in before using this route', Response::HTTP_UNAUTHORIZED);
        }

        return $this->json($user, context: ['groups' => ['read:user:user']]);
    }
}
