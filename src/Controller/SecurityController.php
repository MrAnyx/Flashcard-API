<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', 'api_', format: 'json')]
class SecurityController extends AbstractController
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
