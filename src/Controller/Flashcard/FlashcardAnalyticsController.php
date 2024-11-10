<?php

declare(strict_types=1);

namespace App\Controller\Flashcard;

use App\Controller\AbstractRestController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/analytics', 'api_', format: 'json')]
class FlashcardAnalyticsController extends AbstractRestController
{
}
