<?php

declare(strict_types=1);

namespace App\Controller\Unit;

use App\Controller\AbstractRestController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/analytics', 'api_analytics', format: 'json')]
class UnitAnalyticsController extends AbstractRestController
{
}
