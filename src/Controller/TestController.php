<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\Body;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/_internal', name: 'api_')]
class TestController extends AbstractRestController
{
    #[Route('/test', name: 'test')]
    public function index(
        #[Body('array')] array $body,
    ): JsonResponse {
        return $this->jsonStd($body);
    }
}
