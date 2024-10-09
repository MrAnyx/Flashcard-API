<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractRestController;
use App\Entity\Flashcard;
use App\Exception\ApiException;
use App\OptionsResolver\FlashcardOptionsResolver;
use App\Repository\FlashcardRepository;
use App\Utility\Regex;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin', 'api_admin_', format: 'json')]
class FlashcardAdminController extends AbstractRestController
{
    #[Route('/flashcards', name: 'get_flashcards', methods: ['GET'])]
    public function getAllFlashcards(
        Request $request,
        FlashcardRepository $flashcardRepository,
    ): JsonResponse {
        $pagination = $this->getPaginationParameter(Flashcard::class, $request);
        $filter = $this->getFilterParameter(Flashcard::class, $request);

        $flashcards = $flashcardRepository->paginateAndFilterAll($pagination, $filter);

        return $this->jsonStd($flashcards, context: ['groups' => ['read:flashcard:admin']]);
    }

    #[Route('/flashcards/{id}', name: 'get_flashcard', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getOneFlashcard(int $id, FlashcardRepository $flashcardRepository): JsonResponse
    {
        // Retrieve the flashcard by id
        $flashcard = $flashcardRepository->find($id);

        // Check if the flashcard exists
        if ($flashcard === null) {
            throw new ApiException(Response::HTTP_NOT_FOUND, 'Flashcard with id %d was not found', [$id]);
        }

        return $this->jsonStd($flashcard, context: ['groups' => ['read:flashcard:admin']]);
    }

    #[Route('/flashcards', name: 'create_flashcard', methods: ['POST'])]
    public function createFlashcard(
        Request $request,
        EntityManagerInterface $em,
        FlashcardOptionsResolver $flashcardOptionsResolver,
    ): JsonResponse {
        // Retrieve the request body
        $body = $this->getRequestPayload($request);

        try {
            // Validate the content of the request body
            $data = $flashcardOptionsResolver
                ->configureFront(true)
                ->configureBack(true)
                ->configureDetails(true)
                ->configureUnit(true)
                ->configureFavorite(true)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        // Temporarly create the flashcard
        $flashcard = new Flashcard();
        $flashcard
            ->setFront($data['front'])
            ->setBack($data['back'])
            ->setDetails($data['details'])
            ->setUnit($data['unit'])
            ->setFavorite($data['favorite']);

        // Second validation using the validation constraints
        $this->validateEntity($flashcard);

        // Save the new flashcard
        $em->persist($flashcard);
        $em->flush();

        // Return the flashcard with the the status 201 (Created)
        return $this->jsonStd(
            $flashcard,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('api_admin_get_flashcard', ['id' => $flashcard->getId()])],
            ['groups' => ['read:flashcard:admin']]
        );
    }

    #[Route('/flashcards/{id}', name: 'delete_flashcard', methods: ['DELETE'], requirements: ['id' => Regex::INTEGER])]
    public function deleteFlashcard(int $id, FlashcardRepository $flashcardRepository, EntityManagerInterface $em): JsonResponse
    {
        // Retrieve the flashcard by id
        $flashcard = $flashcardRepository->find($id);

        // Check if the flashcard exists
        if ($flashcard === null) {
            throw new ApiException(Response::HTTP_NOT_FOUND, 'Flashcard with id %d was not found', [$id]);
        }

        // Remove the flashcard
        $em->remove($flashcard);
        $em->flush();

        // Return a response with status 204 (No Content)
        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/flashcards/{id}', name: 'update_flashcard', methods: ['PATCH', 'PUT'], requirements: ['id' => Regex::INTEGER])]
    public function updateFlashcard(
        int $id,
        FlashcardRepository $flashcardRepository,
        EntityManagerInterface $em,
        Request $request,
        FlashcardOptionsResolver $flashcardOptionsResolver,
    ): JsonResponse {
        // Retrieve the flashcard by id
        $flashcard = $flashcardRepository->find($id);

        // Check if the flashcard exists
        if ($flashcard === null) {
            throw new ApiException(Response::HTTP_NOT_FOUND, 'Flashcard with id %d was not found', [$id]);
        }

        // Retrieve the request body
        $body = $this->getRequestPayload($request);

        try {
            // Check if the request method is PUT. In this case, all parameters must be provided in the request body.
            // Otherwise, all parameters are optional.
            $mandatoryParameters = $request->getMethod() === 'PUT';

            // Validate the content of the request body
            $data = $flashcardOptionsResolver
                ->configureFront($mandatoryParameters)
                ->configureBack($mandatoryParameters)
                ->configureDetails($mandatoryParameters)
                ->configureUnit($mandatoryParameters)
                ->configureFavorite($mandatoryParameters)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        // Update each fields if necessary
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'front':
                    $flashcard->setFront($value);
                    break;
                case 'back':
                    $flashcard->setBack($value);
                    break;
                case 'details':
                    $flashcard->setDetails($value);
                    break;
                case 'unit':
                    $flashcard->setUnit($value);
                    break;
                case 'favorite':
                    $flashcard->setFavorite($value);
                    break;
            }
        }

        // Second validation using the validation constraints
        $this->validateEntity($flashcard);

        // Save the flashcard information
        $em->flush();

        // Return the flashcard
        return $this->jsonStd($flashcard, context: ['groups' => ['read:flashcard:admin']]);
    }
}
