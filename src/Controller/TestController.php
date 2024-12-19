<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\RelativeToEntity;
use App\Entity\Topic;
use App\Enum\SettingName;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/_internal', name: 'api_', format: 'json')]
#[RelativeToEntity(Topic::class)]
class TestController extends AbstractRestController
{
    #[Route('/test', name: 'test')]
    public function index(
    ): JsonResponse {
        dd($this->getUserSetting(SettingName::COLOR_THEME));

        return $this->json(null);
    }
}
