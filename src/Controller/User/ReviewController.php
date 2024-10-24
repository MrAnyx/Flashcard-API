<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use App\Entity\User;
use App\Enum\CountCriteria\ReviewCountCriteria;
use App\Repository\ReviewRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', 'api_', format: 'json')]
class ReviewController extends AbstractRestController
{
    #[Route('/reviews/count', name: 'count_reviews', methods: ['GET'])]
    public function countReviews(
        ReviewRepository $reviewRepository,
        Request $request,
    ) {
        $criteria = $this->getCountCriteria($request, ReviewCountCriteria::class, ReviewCountCriteria::ONLY_VALID->value);

        /** @var User $user */
        $user = $this->getUser();

        $count = match ($criteria) {
            ReviewCountCriteria::ALL => $reviewRepository->countReviews($user, true),
            ReviewCountCriteria::ONLY_VALID => $reviewRepository->countReviews($user, false),
        };

        return $this->jsonStd($count);
    }
}
