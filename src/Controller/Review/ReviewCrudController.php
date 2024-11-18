<?php

declare(strict_types=1);

namespace App\Controller\Review;

use App\Attribute\Resource;
use App\Controller\AbstractRestController;
use App\Entity\Session;
use App\Repository\ReviewRepository;
use App\Utility\Regex;
use App\Voter\SessionVoter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', 'api_', format: 'json')]
class ReviewCrudController extends AbstractRestController
{
    #[Route('/sessions/{id}/reviews', name: 'get_reviews_by_session', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getReviewsBySession(
        ReviewRepository $reviewRepository,
        #[Resource(SessionVoter::OWNER)] Session $session,
    ): JsonResponse {
        $reviews = $reviewRepository->findAllBySession($session, true);

        return $this->json($reviews, context: [
            'groups' => ['read:review:user'],
        ]);
    }
}
