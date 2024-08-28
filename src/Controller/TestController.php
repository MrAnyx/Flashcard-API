<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Setting;
use App\Enum\JsonStandardStatus;
use App\Enum\SettingName;
use App\Setting\SettingEntry;
use App\Setting\Type\EnumType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/_internal', name: 'api_')]
class TestController extends AbstractRestController
{
    #[Route('/test', name: 'test')]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $entry = new SettingEntry(SettingName::TEST_ENUM, JsonStandardStatus::VALID, EnumType::class, [], ['class' => JsonStandardStatus::class]);

        $setting = new Setting($entry, $this->getUser());

        $em->persist($setting);
        $em->flush();

        return $this->jsonStd($setting);
    }
}
