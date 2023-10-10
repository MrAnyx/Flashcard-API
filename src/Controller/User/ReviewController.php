<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\ReviewRepository;
use App\Controller\AbstractRestController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', 'api_', format: 'json')]
class ReviewController extends AbstractRestController
{
    #[Route('/reviews/count', name: 'get_reviews', methods: ['GET'])]
    public function getCountReviews(ReviewRepository $reviewRepository)
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->json([
            'actual' => $reviewRepository->countReviews($user, false),
            'total' => $reviewRepository->countReviews($user, true),
        ]);
    }
}
