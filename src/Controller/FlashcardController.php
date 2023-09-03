<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Utility\Regex;
use App\Entity\Flashcard;
use App\Service\EntityChecker;
use App\Exception\ApiException;
use App\Service\RequestPayloadService;
use App\Repository\FlashcardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\OptionsResolver\FlashcardOptionsResolver;
use App\OptionsResolver\PaginatorOptionsResolver;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', 'api_', format: 'json')]
class FlashcardController extends AbstractRestController
{
    // #[Route('/flashcards', name: 'get_flashcards', methods: ['GET'])]
    // public function getAllFlashcards(
    //     Request $request,
    //     FlashcardRepository $flashcardRepository,
    //     PaginatorOptionsResolver $paginatorOptionsResolver,
    //     EntityChecker $entityChecker
    // ): JsonResponse {

    //     $sortableFields = $entityChecker->getSortableFields(Flashcard::class);

    //     try {
    //         $queryParams = $paginatorOptionsResolver
    //             ->configurePage()
    //             ->configureSort($sortableFields)
    //             ->configureOrder()
    //             ->resolve($request->query->all());
    //     } catch (Exception $e) {
    //         throw new ApiException($e->getMessage());
    //     }

    //     /** @var User $user Here the user is not null because we use the attribut IsGranted("IS_AUTHENTICATED") so the user must be authenticated */
    //     $user = $this->getUser();

    //     $flashcards = $flashcardRepository->findAllWithPagination(
    //         $queryParams['page'],
    //         $queryParams['sort'],
    //         $queryParams['order']
    //     );

    //     return $this->json($flashcards);
    // }

    // #[Route('/flashcards/{id}', name: 'get_flashcard', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    // public function getOneFlashcard(int $id, FlashcardRepository $flashcardRepository): JsonResponse
    // {
    //     // Retrieve the flashcard by id
    //     $flashcard = $flashcardRepository->find($id);

    //     // Check if the flashcard exists
    //     if ($flashcard === null) {
    //         throw new ApiException("Flashcard with id $id was not found", Response::HTTP_NOT_FOUND);
    //     }

    //     return $this->json($flashcard);
    // }

    // #[Route('/flashcards', name: 'create_flashcard', methods: ['POST'])]
    // public function createFlashcard(
    //     Request $request,
    //     EntityManagerInterface $em,
    //     ValidatorInterface $validator,
    //     FlashcardOptionsResolver $flashcardOptionsResolver,
    //     RequestPayloadService $requestPayloadService
    // ): JsonResponse {

    //     try {
    //         // Retrieve the request body
    //         $body = $requestPayloadService->getRequestPayload($request);

    //         // Validate the content of the request body
    //         $data = $flashcardOptionsResolver
    //             ->configureFront(true)
    //             ->configureBack(true)
    //             ->configureDetails(true)
    //             ->resolve($body);
    //     } catch (Exception $e) {
    //         throw new ApiException($e->getMessage(), Response::HTTP_BAD_REQUEST);
    //     }

    //     // Temporarly create the flashcard
    //     $flashcard = new Flashcard();
    //     $flashcard
    //         ->setFront($data['front'])
    //         ->setBack($data['back'])
    //         ->setDetails($data['details'])
    //         ->setAuthor($this->getUser());

    //     // Second validation using the validation constraints
    //     $errors = $validator->validate($flashcard, groups: ['Default']);
    //     if (count($errors) > 0) {
    //         throw new ApiException((string) $errors[0]->getMessage(), Response::HTTP_BAD_REQUEST);
    //     }

    //     // Save the new flashcard
    //     $em->persist($flashcard);
    //     $em->flush();

    //     // Return the flashcard with the the status 201 (Created)
    //     return $this->json($flashcard, Response::HTTP_CREATED, [
    //         'Location' => $this->generateUrl('api_get_flashcard', ['id' => $flashcard->getId()]),
    //     ]);
    // }

    // #[Route('/flashcards/{id}', name: 'delete_flashcard', methods: ['DELETE'], requirements: ['id' => Regex::INTEGER])]
    // public function deleteFlashcard(int $id, FlashcardRepository $flashcardRepository, EntityManagerInterface $em): JsonResponse
    // {
    //     // Retrieve the flashcard by id
    //     $flashcard = $flashcardRepository->find($id);

    //     // Check if the flashcard exists
    //     if ($flashcard === null) {
    //         throw new ApiException("Flashcard with id $id was not found", Response::HTTP_NOT_FOUND);
    //     }

    //     // Remove the flashcard
    //     $em->remove($flashcard);
    //     $em->flush();

    //     // Return a response with status 204 (No Content)
    //     return $this->json(null, Response::HTTP_NO_CONTENT);
    // }

    // #[Route('/flashcards/{id}', name: 'update_flashcard', methods: ['PATCH', 'PUT'], requirements: ['id' => Regex::INTEGER])]
    // public function updateFlashcard(
    //     int $id,
    //     FlashcardRepository $flashcardRepository,
    //     EntityManagerInterface $em,
    //     RequestPayloadService $requestPayloadService,
    //     Request $request,
    //     FlashcardOptionsResolver $flashcardOptionsResolver,
    //     ValidatorInterface $validator
    // ): JsonResponse {

    //     // Retrieve the flashcard by id
    //     $flashcard = $flashcardRepository->find($id);

    //     // Check if the flashcard exists
    //     if ($flashcard === null) {
    //         throw new ApiException("Flashcard with id $id was not found", Response::HTTP_NOT_FOUND);
    //     }

    //     try {
    //         // Retrieve the request body
    //         $body = $requestPayloadService->getRequestPayload($request);

    //         // Check if the request method is PUT. In this case, all parameters must be provided in the request body.
    //         // Otherwise, all parameters are optional.
    //         $mandatoryParameters = $request->getMethod() === 'PUT';

    //         // Validate the content of the request body
    //         $data = $flashcardOptionsResolver
    //             ->configureFront($mandatoryParameters)
    //             ->configureBack($mandatoryParameters)
    //             ->configureDetails($mandatoryParameters)
    //             ->resolve($body);
    //     } catch (Exception $e) {
    //         throw new ApiException($e->getMessage(), Response::HTTP_BAD_REQUEST);
    //     }

    //     // Update each fields if necessary
    //     foreach ($data as $field => $value) {
    //         switch ($field) {
    //             case 'front':
    //                 $flashcard->setFront($value);
    //                 break;
    //             case 'back':
    //                 $flashcard->setBack($value);
    //                 break;
    //             case 'details':
    //                 $flashcard->setDetails($value);
    //                 break;

    //                 // case 'author':
    //                 //     $flashcard->setAuthor($value);
    //                 //     break;
    //         }
    //     }

    //     // Second validation using the validation constraints
    //     $errors = $validator->validate($flashcard, groups: ['Default']);
    //     if (count($errors) > 0) {
    //         throw new ApiException((string) $errors[0]->getMessage(), Response::HTTP_BAD_REQUEST);
    //     }

    //     // Save the flashcard information
    //     $em->flush();

    //     // Return the flashcard
    //     return $this->json($flashcard);
    // }
}
