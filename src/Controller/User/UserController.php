<?php

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', 'api_', format: 'json')]
class UserController extends AbstractRestController
{
    #[Route('/users/me', name: 'get_me', methods: ['GET'])]
    public function getMe()
    {
        $user = $this->getUser();

        return $this->json($user, context: ['groups' => ['read:user:user']]);
    }
}
