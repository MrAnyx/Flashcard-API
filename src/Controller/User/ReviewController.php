<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use App\Entity\User;
use App\Exception\ApiException;
use App\OptionsResolver\ReviewOptionsResolver;
use App\Repository\ReviewRepository;
use App\Service\RequestPayloadService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', 'api_', format: 'json')]
class ReviewController extends AbstractRestController
{
    #[Route('/reviews/count', name: 'count_reviews', methods: ['GET'])]
    public function getCountReviews(
        ReviewRepository $reviewRepository,
        ReviewOptionsResolver $reviewOptionsResolver,
        RequestPayloadService $requestPayloadService,
        Request $request
    ) {
        try {
            // Retrieve the request body
            $query = $requestPayloadService->getQueryPayload($request);

            // Validate the content of the request body
            $data = $reviewOptionsResolver
                ->configureWithReset(false)
                ->resolve($query);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        /** @var User $user */
        $user = $this->getUser();
        $countReviews = $reviewRepository->countReviews($user, $data['withReset']);

        return $this->jsonStd($countReviews);
    }
}
