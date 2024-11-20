<?php

declare(strict_types=1);

namespace App\Controller\Session;

use App\Attribute\RelativeToEntity;
use App\Controller\AbstractRestController;
use App\Entity\Session;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', 'api_', format: 'json')]
#[RelativeToEntity(Session::class)]
class SessionAnalyticsController extends AbstractRestController
{
}
