<?php

declare(strict_types=1);

namespace App\Controller\Unit;

use App\Attribute\RelativeToEntity;
use App\Controller\AbstractRestController;
use App\Entity\Unit;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/analytics', 'api_analytics', format: 'json')]
#[RelativeToEntity(Unit::class)]
class UnitAnalyticsController extends AbstractRestController
{
}
