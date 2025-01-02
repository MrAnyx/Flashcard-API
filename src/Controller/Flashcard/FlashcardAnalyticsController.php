<?php

declare(strict_types=1);

namespace App\Controller\Flashcard;

use App\Attribute\RelativeToEntity;
use App\Controller\AbstractRestController;
use App\Entity\Flashcard;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/analytics', 'api_', format: 'json')]
#[RelativeToEntity(Flashcard::class)]
class FlashcardAnalyticsController extends AbstractRestController
{
}
