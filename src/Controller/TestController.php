<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/_internal', name: 'api_')]
class TestController extends AbstractRestController
{
    #[Route('/test', name: 'test')]
    public function index(Request $request): JsonResponse
    {
        throw new \Exception('Test');

        return $this->jsonStd($request);
    }
}
