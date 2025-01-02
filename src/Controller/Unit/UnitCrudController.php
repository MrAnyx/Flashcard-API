<?php

declare(strict_types=1);

namespace App\Controller\Unit;

use App\Attribute\RelativeToEntity;
use App\Attribute\Resource;
use App\Controller\AbstractRestController;
use App\Entity\Topic;
use App\Entity\Unit;
use App\Entity\User;
use App\Model\Filter;
use App\Model\Page;
use App\Modifier\Modifier;
use App\Modifier\Transformer\EntityByIdTransformer;
use App\Repository\UnitRepository;
use App\Utility\Regex;
use App\Voter\TopicVoter;
use App\Voter\UnitVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(name: 'api_', format: 'json')]
#[RelativeToEntity(Unit::class)]
class UnitCrudController extends AbstractRestController
{
    #[Route('/units', name: 'get_units', methods: ['GET'])]
    public function getUnits(
        UnitRepository $unitRepository,
        Page $page,
        ?Filter $filter,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $units = $unitRepository->paginateAndFilterAll($page, $filter, $user);

        return $this->json($units, context: ['groups' => ['read:unit:user', 'read:topic:user', 'read:pagination']]);
    }

    #[Route('/units/{id}', name: 'get_unit', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getUnit(
        #[Resource(UnitVoter::OWNER)] Unit $unit,
    ): JsonResponse {
        return $this->json($unit, context: ['groups' => ['read:unit:user', 'read:topic:user']]);
    }

    #[Route('/units', name: 'create_unit', methods: ['POST'])]
    public function createUnit(
        EntityManagerInterface $em,
    ): JsonResponse {
        $unit = $this->decodeBody(
            classname: Unit::class,
            deserializationGroups: ['write:unit:user'],
            transformers: [
                new Modifier('topic', EntityByIdTransformer::class, ['entity' => Topic::class, 'voter' => TopicVoter::OWNER]),
            ]
        );

        $em->persist($unit);
        $em->flush();

        return $this->json(
            $unit,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('api_get_unit', ['id' => $unit->getId()])],
            ['groups' => ['read:unit:user', 'read:topic:user']]
        );
    }

    #[Route('/units/{id}', name: 'delete_unit', methods: ['DELETE'], requirements: ['id' => Regex::INTEGER])]
    public function deleteUnit(
        EntityManagerInterface $em,
        #[Resource(UnitVoter::OWNER)] Unit $unit,
    ): JsonResponse {
        $em->remove($unit);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/units/{id}', name: 'update_unit', methods: ['PATCH', 'PUT'], requirements: ['id' => Regex::INTEGER])]
    public function updateUnit(
        EntityManagerInterface $em,
        #[Resource(UnitVoter::OWNER)] Unit $unit,
    ): JsonResponse {
        $updatedUnit = $this->decodeBody(
            classname: Unit::class,
            fromObject: $unit,
            deserializationGroups: ['write:unit:user'],
            transformers: [
                new Modifier('topic', EntityByIdTransformer::class, ['entity' => Topic::class, 'voter' => TopicVoter::OWNER]),
            ]
        );

        $em->flush();

        return $this->json($updatedUnit, context: ['groups' => ['read:unit:user', 'read:topic:user']]);
    }

    #[Route('/topics/{id}/units', name: 'get_units_by_topic', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getUnitsByTopic(
        UnitRepository $unitRepository,
        Page $page,
        ?Filter $filter,
        #[Resource(TopicVoter::OWNER)] Topic $topic,
    ): JsonResponse {
        $units = $unitRepository->paginateAndFilterByTopic($page, $filter, $topic);

        return $this->json($units, context: ['groups' => ['read:unit:user', 'read:topic:user', 'read:pagination']]);
    }

    #[Route('/units/recent', name: 'recent_units', methods: ['GET'])]
    public function getRecentUnits(
        UnitRepository $unitRepository,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $recentUnits = $unitRepository->findRecentUnitsByTopic($user, null, 5);

        return $this->json($recentUnits, context: ['groups' => ['read:unit:user', 'read:topic:user']]);
    }

    #[Route('/topics/{id}/units/recent', name: 'recent_units_by_topic', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getRecentUnitsByTopic(
        UnitRepository $unitRepository,
        #[Resource(TopicVoter::OWNER)] Topic $topic,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $recentUnits = $unitRepository->findRecentUnitsByTopic($user, $topic, 4);

        return $this->json($recentUnits, context: ['groups' => ['read:unit:user', 'read:topic:user']]);
    }
}
