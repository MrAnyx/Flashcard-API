<?php

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth', 'api_auth_', format: 'json')]
class SecurityController extends AbstractRestController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(): void
    {
        // This method is not executed
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(): void
    {
        // This method is not executed
    }
}
