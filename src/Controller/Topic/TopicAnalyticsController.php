<?php

declare(strict_types=1);

namespace App\Controller\Topic;

use App\Attribute\RelativeToEntity;
use App\Controller\AbstractRestController;
use App\Entity\Topic;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/analytics', 'api_analytics', format: 'json')]
#[RelativeToEntity(Topic::class)]
class TopicAnalyticsController extends AbstractRestController
{
}
