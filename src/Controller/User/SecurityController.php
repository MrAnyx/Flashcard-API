<?php

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth', 'api_', format: 'json')]
class SecurityController extends AbstractRestController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(): void
    {
        // This method is not executed
    }

    #[Route('/token/refresh', name: 'token_refresh', methods: ['POST'])]
    public function tokenRefresh(): void
    {
        // This method is not executed
    }
}
