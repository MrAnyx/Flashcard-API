<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Attribute\Resource;
use App\Controller\AbstractRestController;
use App\Entity\Session;
use App\Entity\User;
use App\Enum\CountCriteria\ReviewCountCriteria;
use App\Repository\ReviewRepository;
use App\Service\PeriodService;
use App\Utility\Regex;
use App\Voter\SessionVoter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
class ReviewController extends AbstractRestController
{
    #[Route('/reviews/count', name: 'count_reviews', methods: ['GET'])]
    public function countReviews(
        ReviewRepository $reviewRepository,
        Request $request,
        PeriodService $periodService,
        #[CurrentUser] User $user,
    ) {
        $criteria = $this->getCountCriteria($request, ReviewCountCriteria::class, ReviewCountCriteria::ONLY_VALID->value);
        $periodType = $this->getPeriodParameter($request);

        $period = $periodService->getDateTimePeriod($periodType);

        $count = match ($criteria) {
            ReviewCountCriteria::ALL => $reviewRepository->countReviews($user, $period, true),
            ReviewCountCriteria::ONLY_VALID => $reviewRepository->countReviews($user, $period, false),
            ReviewCountCriteria::GROUP_BY_DATE => $reviewRepository->countAllByDate($user, $period),
        };

        return $this->jsonStd($count);
    }

    #[Route('/sessions/{id}/reviews', name: 'get_reviews_by_session', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getReviewsBySession(
        ReviewRepository $reviewRepository,
        #[Resource(SessionVoter::OWNER)] Session $session,
    ): JsonResponse {
        $reviews = $reviewRepository->findAllBySession($session, true);

        return $this->jsonStd($reviews, context: [
            'groups' => ['read:review:user'],
        ]);
    }
}
