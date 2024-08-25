<?php

declare(strict_types=1);

namespace App\SpacedRepetition;

use App\Entity\Flashcard;

class FSRSv4_5Algorithm extends FSRSv4Algorithm
{
    protected function getWeights(): array
    {
        return [0.4872, 1.4003, 3.7145, 13.8206, 5.1618, 1.2298, 0.8975, 0.031, 1.6474, 0.1367, 1.0461, 2.1072, 0.0793, 0.3246, 1.587, 0.2272, 2.8755];
    }

    protected function getRetrievability(Flashcard $flashcard): float
    {
        $elapsedDays = (int) $flashcard->getPreviousReview()->diff(new \DateTimeImmutable())->format('%a');
        $factor = 19 / 81;
        $decay = -0.5;

        return (1 + ($factor * $elapsedDays / $flashcard->getStability())) ** $decay;
    }

    protected function nextInterval(Flashcard $flashcard): int
    {
        $interval = 9 * $flashcard->getStability() * ((1 / $this->requestRetention) - 1);

        return min(max((int) round($interval), 1), self::MAX_INTERVAL);
    }
}
