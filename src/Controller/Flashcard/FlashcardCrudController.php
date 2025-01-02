<?php

declare(strict_types=1);

namespace App\Controller\Flashcard;

use App\Attribute\RelativeToEntity;
use App\Attribute\Resource;
use App\Controller\AbstractRestController;
use App\Entity\Flashcard;
use App\Entity\Unit;
use App\Entity\User;
use App\Model\Filter;
use App\Model\Page;
use App\Modifier\Modifier;
use App\Modifier\Transformer\EntityByIdTransformer;
use App\Repository\FlashcardRepository;
use App\Utility\Regex;
use App\Voter\FlashcardVoter;
use App\Voter\UnitVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(name: 'api_', format: 'json')]
#[RelativeToEntity(Flashcard::class)]
class FlashcardCrudController extends AbstractRestController
{
    #[Route('/flashcards', name: 'get_flashcards', methods: ['GET'])]
    public function getFlashcards(
        FlashcardRepository $flashcardRepository,
        Page $page,
        ?Filter $filter,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $flashcards = $flashcardRepository->paginateAndFilterAll($page, $filter, $user);

        return $this->json($flashcards, context: ['groups' => ['read:flashcard:user', 'read:unit:user', 'read:topic:user', 'read:pagination']]);
    }

    #[Route('/flashcards/{id}', name: 'get_flashcard', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getFlashcard(
        #[Resource(FlashcardVoter::OWNER)] Flashcard $flashcard,
    ): JsonResponse {
        return $this->json($flashcard, context: ['groups' => ['read:flashcard:user', 'read:unit:user', 'read:topic:user']]);
    }

    #[Route('/flashcards', name: 'create_flashcard', methods: ['POST'])]
    public function createFlashcard(
        EntityManagerInterface $em,
    ): JsonResponse {
        $flashcard = $this->decodeBody(
            classname: Flashcard::class,
            deserializationGroups: ['write:flashcard:user'],
            transformers: [
                new Modifier('unit', EntityByIdTransformer::class, ['entity' => Unit::class, 'voter' => UnitVoter::OWNER]),
            ]
        );

        $this->validateEntity($flashcard);
        $em->persist($flashcard);
        $em->flush();

        return $this->json(
            $flashcard,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('api_get_flashcard', ['id' => $flashcard->getId()])],
            ['groups' => ['read:flashcard:user']]
        );
    }

    #[Route('/flashcards/{id}', name: 'delete_flashcard', methods: ['DELETE'], requirements: ['id' => Regex::INTEGER])]
    public function deleteFlashcard(
        EntityManagerInterface $em,
        #[Resource(FlashcardVoter::OWNER)] Flashcard $flashcard,
    ): JsonResponse {
        $em->remove($flashcard);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/flashcards/{id}', name: 'update_flashcard', methods: ['PATCH', 'PUT'], requirements: ['id' => Regex::INTEGER])]
    public function updateFlashcard(
        EntityManagerInterface $em,
        #[Resource(FlashcardVoter::OWNER)] Flashcard $flashcard,
    ): JsonResponse {
        $updatedFlashcard = $this->decodeBody(
            classname: Flashcard::class,
            fromObject: $flashcard,
            deserializationGroups: ['write:flashcard:user'],
            transformers: [
                new Modifier('unit', EntityByIdTransformer::class, ['entity' => Unit::class, 'voter' => UnitVoter::OWNER]),
            ]
        );

        $this->validateEntity($updatedFlashcard);
        $em->flush();

        return $this->json($updatedFlashcard, context: ['groups' => ['read:flashcard:user', 'read:unit:user', 'read:topic:user']]);
    }

    #[Route('/units/{id}/flashcards', name: 'get_flashcards_by_unit', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getFlashcardsByUnit(
        FlashcardRepository $flashcardRepository,
        Page $page,
        ?Filter $filter,
        #[Resource(UnitVoter::OWNER)] Unit $unit,
    ): JsonResponse {
        $flashcards = $flashcardRepository->paginateAndFilterByUnit($page, $filter, $unit);

        return $this->json($flashcards, context: ['groups' => ['read:flashcard:user', 'read:unit:user', 'read:topic:user', 'read:pagination']]);
    }
}
