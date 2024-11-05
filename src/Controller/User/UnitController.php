<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Attribute\Body;
use App\Attribute\RelativeToEntity;
use App\Attribute\Resource;
use App\Controller\AbstractRestController;
use App\Entity\Session;
use App\Entity\Topic;
use App\Entity\Unit;
use App\Entity\User;
use App\Enum\CountCriteria\UnitCountCriteria;
use App\Enum\SettingName;
use App\Model\Filter;
use App\Model\Page;
use App\OptionsResolver\UnitOptionsResolver;
use App\Repository\FlashcardRepository;
use App\Repository\ReviewRepository;
use App\Repository\UnitRepository;
use App\Utility\Regex;
use App\Voter\TopicVoter;
use App\Voter\UnitVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
class UnitController extends AbstractRestController
{
    #[Route('/units', name: 'get_units', methods: ['GET'])]
    public function getUnits(
        UnitRepository $unitRepository,
        #[RelativeToEntity(Unit::class)] Page $page,
        #[RelativeToEntity(Unit::class)] Filter $filter,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $units = $unitRepository->paginateAndFilterAll($page, $filter, $user);

        return $this->jsonStd($units, context: ['groups' => ['read:unit:user']]);
    }

    #[Route('/units/{id}', name: 'get_unit', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getUnit(
        #[Resource(UnitVoter::OWNER)] Unit $unit,
    ): JsonResponse {
        return $this->jsonStd($unit, context: ['groups' => ['read:unit:user']]);
    }

    #[Route('/units', name: 'create_unit', methods: ['POST'])]
    public function createUnit(
        EntityManagerInterface $em,
        UnitOptionsResolver $unitOptionsResolver,
        #[Body] mixed $body,
    ): JsonResponse {
        try {
            // Validate the content of the request body
            $data = $unitOptionsResolver
                ->configureName(true)
                ->configureTopic(true)
                ->configureDescription(true)
                ->configureFavorite(true)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        $this->denyAccessUnlessGranted(TopicVoter::OWNER, $data['topic'], 'You can not use this resource');

        // Temporarly create the element
        $unit = new Unit();
        $unit
            ->setName($data['name'])
            ->setTopic($data['topic'])
            ->setDescription($data['description'])
            ->setFavorite($data['favorite']);

        // Second validation using the validation constraints
        $this->validateEntity($unit);

        // Save the new element
        $em->persist($unit);
        $em->flush();

        // Return the element with the the status 201 (Created)
        return $this->jsonStd(
            $unit,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('api_get_unit', ['id' => $unit->getId()])],
            ['groups' => ['read:unit:user']]
        );
    }

    #[Route('/units/{id}', name: 'delete_unit', methods: ['DELETE'], requirements: ['id' => Regex::INTEGER])]
    public function deleteUnit(
        EntityManagerInterface $em,
        #[Resource(UnitVoter::OWNER)] Unit $unit,
    ): JsonResponse {
        $em->remove($unit);
        $em->flush();

        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/units/{id}', name: 'update_unit', methods: ['PATCH', 'PUT'], requirements: ['id' => Regex::INTEGER])]
    public function updateUnit(
        EntityManagerInterface $em,
        Request $request,
        UnitOptionsResolver $unitOptionsResolver,
        #[Resource(UnitVoter::OWNER)] Unit $unit,
        #[Body] mixed $body,
    ): JsonResponse {
        try {
            // Check if the request method is PUT. In this case, all parameters must be provided in the request body.
            // Otherwise, all parameters are optional.
            $mandatoryParameters = $request->getMethod() === 'PUT';

            // Validate the content of the request body
            $data = $unitOptionsResolver
                ->configureName($mandatoryParameters)
                ->configureTopic($mandatoryParameters)
                ->configureDescription($mandatoryParameters)
                ->configureFavorite($mandatoryParameters)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        // Update each fields if necessary
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'name':
                    $unit->setName($value);
                    break;
                case 'description':
                    $unit->setDescription($value);
                    break;
                case 'topic':
                    $this->denyAccessUnlessGranted(TopicVoter::OWNER, $value, 'You can not use this resource');
                    $unit->setTopic($value);
                    break;
                case 'favorite':
                    $unit->setFavorite($value);
                    break;
            }
        }

        // Second validation using the validation constraints
        $this->validateEntity($unit);

        // Save the element information
        $em->flush();

        // Return the element
        return $this->jsonStd($unit, context: ['groups' => ['read:unit:user']]);
    }

    #[Route('/topics/{id}/units', name: 'get_units_by_topic', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getUnitsByTopic(
        UnitRepository $unitRepository,
        #[RelativeToEntity(Unit::class)] Page $page,
        #[RelativeToEntity(Unit::class)] Filter $filter,
        #[Resource(TopicVoter::OWNER)] Topic $topic,
    ): JsonResponse {
        $units = $unitRepository->paginateAndFilterByTopic($page, $filter, $topic);

        return $this->jsonStd($units, context: ['groups' => ['read:unit:user']]);
    }

    #[Route('/units/{id}/reset', name: 'reset_unit', methods: ['PATCH'], requirements: ['id' => Regex::INTEGER])]
    public function resetUnit(
        ReviewRepository $reviewRepository,
        FlashcardRepository $flashcardRepository,
        #[Resource(UnitVoter::OWNER)] Unit $unit,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $reviewRepository->resetBy($user, $unit);
        $flashcardRepository->resetBy($user, $unit);

        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/units/{id}/session', name: 'session_unit', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getFlashcardSession(
        FlashcardRepository $flashcardRepository,
        EntityManagerInterface $em,
        #[Resource(UnitVoter::OWNER)] Unit $unit,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $cardsToReview = $flashcardRepository->findFlashcardToReviewBy($unit, $user, $this->getUserSetting(SettingName::FLASHCARD_PER_SESSION));

        if (\count($cardsToReview) === 0) {
            return $this->jsonStd([
                'session' => null,
                'flashcards' => [],
            ]);
        }

        shuffle($cardsToReview);

        $session = new Session();
        $session->setAuthor($user);
        $this->validateEntity($session);
        $em->persist($session);
        $em->flush();

        return $this->jsonStd([
            'session' => $session,
            'flashcards' => $cardsToReview,
        ], context: ['groups' => ['read:flashcard:user', 'read:session:user']]);
    }

    #[Route('/units/recent', name: 'recent_units', methods: ['GET'])]
    public function getRecentUnits(
        UnitRepository $unitRepository,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $recentUnits = $unitRepository->findRecentUnitsByTopic($user, null, 5);

        return $this->jsonStd($recentUnits, context: ['groups' => ['read:unit:user']]);
    }

    #[Route('/topics/{id}/units/recent', name: 'recent_units_by_topic', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getRecentUnitsByTopic(
        UnitRepository $unitRepository,
        #[Resource(TopicVoter::OWNER)] Topic $topic,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $recentUnits = $unitRepository->findRecentUnitsByTopic($user, $topic, 4);

        return $this->jsonStd($recentUnits, context: ['groups' => ['read:unit:user']]);
    }

    #[Route('/units/count', name: 'unit_count', methods: ['GET'])]
    public function countUnits(
        UnitRepository $unitRepository,
        Request $request,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $criteria = $this->getCountCriteria($request, UnitCountCriteria::class, UnitCountCriteria::ALL->value);

        $count = match ($criteria) {
            UnitCountCriteria::ALL => $unitRepository->countAll($user),
        };

        return $this->jsonStd($count);
    }
}
