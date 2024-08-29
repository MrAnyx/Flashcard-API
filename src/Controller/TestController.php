<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/_internal', name: 'api_')]
class TestController extends AbstractRestController
{
    #[Route('/test', name: 'test')]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        return $this->jsonStd('Hello World!');
    }
}
