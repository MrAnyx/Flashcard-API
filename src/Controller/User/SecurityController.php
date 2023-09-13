<?php

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth', 'api_', format: 'json')]
class SecurityController extends AbstractRestController
{
    #[Route('/login', name: 'login')]
    public function login(): void
    {
        // This method is not executed
    }

    #[Route('/token/refresh', name: 'token_refresh')]
    public function tokenRefresh(): void
    {
        // This method is not executed
    }
}
