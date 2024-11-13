<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\RelativeToEntity;
use App\Entity\Topic;
use App\Model\Page;
use App\ValueResolver\PageResolver;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/_internal', name: 'api_')]
#[RelativeToEntity(Topic::class)]
class TestController extends AbstractRestController
{
    #[Route('/test', name: 'test')]
    public function index(
        #[ValueResolver(PageResolver::class)]
        ?Page $page,
    ): JsonResponse {
        dd($page);

        return $this->jsonStd(null);
    }
}
