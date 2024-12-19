<?php

declare(strict_types=1);

namespace App\Controller\Review;

use App\Attribute\RelativeToEntity;
use App\Controller\AbstractRestController;
use App\Entity\Review;
use App\Entity\User;
use App\Enum\CountCriteria\ReviewCountCriteria;
use App\Model\Period;
use App\Repository\ReviewRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
#[RelativeToEntity(Review::class)]
class ReviewScalarController extends AbstractRestController
{
    #[Route('/reviews/count/{criteria}', name: 'count_reviews', methods: ['GET'])]
    public function countReviews(
        ReviewRepository $reviewRepository,
        ?Period $period,
        #[CurrentUser] User $user,
        ReviewCountCriteria $criteria = ReviewCountCriteria::ONLY_VALID,
    ) {
        $count = match ($criteria) {
            ReviewCountCriteria::ALL => $reviewRepository->countReviews($user, $period, true),
            ReviewCountCriteria::ONLY_VALID => $reviewRepository->countReviews($user, $period, false),
            ReviewCountCriteria::GROUP_BY_DATE => $reviewRepository->countAllByDate($user, $period),
        };

        return $this->json($count);
    }
}
