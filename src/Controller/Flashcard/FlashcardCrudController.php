<?php

declare(strict_types=1);

namespace App\Controller\Flashcard;

use App\Attribute\Body;
use App\Attribute\RelativeToEntity;
use App\Attribute\Resource;
use App\Controller\AbstractRestController;
use App\Entity\Flashcard;
use App\Entity\Unit;
use App\Entity\User;
use App\Model\Filter;
use App\Model\Page;
use App\OptionsResolver\FlashcardOptionsResolver;
use App\Repository\FlashcardRepository;
use App\Utility\Regex;
use App\ValueResolver\BodyResolver;
use App\Voter\FlashcardVoter;
use App\Voter\UnitVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
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

        return $this->json($flashcards, context: ['groups' => ['read:flashcard:user']]);
    }

    #[Route('/flashcards/{id}', name: 'get_flashcard', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getFlashcard(
        #[Resource(FlashcardVoter::OWNER)] Flashcard $flashcard,
    ): JsonResponse {
        return $this->json($flashcard, context: ['groups' => ['read:flashcard:user']]);
    }

    #[Route('/flashcards', name: 'create_flashcard', methods: ['POST'])]
    public function createFlashcard(
        EntityManagerInterface $em,
        FlashcardOptionsResolver $flashcardOptionsResolver,
        #[Body, ValueResolver(BodyResolver::class)] mixed $body,
    ): JsonResponse {
        try {
            // Validate the content of the request body
            $data = $flashcardOptionsResolver
                ->configureFront(true)
                ->configureBack(true)
                ->configureDetails(true)
                ->configureUnit(true)
                ->configureFavorite(true)
                ->configureHelp(true)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        $this->denyAccessUnlessGranted(UnitVoter::OWNER, $data['unit'], 'You can not use this resource');

        // Temporarly create the flashcard
        $flashcard = new Flashcard();
        $flashcard
            ->setFront($data['front'])
            ->setBack($data['back'])
            ->setDetails($data['details'])
            ->setUnit($data['unit'])
            ->setFavorite($data['favorite'])
            ->setHelp($data['help']);

        // Second validation using the validation constraints
        $this->validateEntity($flashcard);

        // Save the new flashcard
        $em->persist($flashcard);
        $em->flush();

        // Return the flashcard with the the status 201 (Created)
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
        // Remove the flashcard
        $em->remove($flashcard);
        $em->flush();

        // Return a response with status 204 (No Content)
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/flashcards/{id}', name: 'update_flashcard', methods: ['PATCH', 'PUT'], requirements: ['id' => Regex::INTEGER])]
    public function updateFlashcard(
        EntityManagerInterface $em,
        Request $request,
        FlashcardOptionsResolver $flashcardOptionsResolver,
        #[Resource(FlashcardVoter::OWNER)] Flashcard $flashcard,
        #[Body, ValueResolver(BodyResolver::class)] mixed $body,
    ): JsonResponse {
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
                ->configureHelp($mandatoryParameters)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
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
                    $this->denyAccessUnlessGranted(UnitVoter::OWNER, $value, 'You can not use this resource');
                    $flashcard->setUnit($value);
                    break;
                case 'favorite':
                    $flashcard->setFavorite($value);
                    break;
                case 'help':
                    $flashcard->setHelp($value);
                    break;
            }
        }

        // Second validation using the validation constraints
        $this->validateEntity($flashcard);

        // Save the flashcard information
        $em->flush();

        // Return the flashcard
        return $this->json($flashcard, context: ['groups' => ['read:flashcard:user']]);
    }

    #[Route('/units/{id}/flashcards', name: 'get_flashcards_by_unit', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getFlashcardsByUnit(
        FlashcardRepository $flashcardRepository,
        Page $page,
        ?Filter $filter,
        #[Resource(UnitVoter::OWNER)] Unit $unit,
    ): JsonResponse {
        $flashcards = $flashcardRepository->paginateAndFilterByUnit($page, $filter, $unit);

        return $this->json($flashcards, context: ['groups' => ['read:flashcard:user']]);
    }
}
