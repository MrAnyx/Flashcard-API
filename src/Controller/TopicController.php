<?php

namespace App\Controller;

use Exception;
use App\Entity\Topic;
use App\Utility\Regex;
use App\Exception\ApiException;
use App\Repository\TopicRepository;
use App\Service\RequestPayloadService;
use Doctrine\ORM\EntityManagerInterface;
use App\OptionsResolver\TopicOptionsResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\OptionsResolver\PaginatorOptionsResolver;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', 'api_', format: 'json')]
#[IsGranted('IS_AUTHENTICATED', message: 'Access denied: You can access this ressource')]
class TopicController extends AbstractController
{
    #[Route('/topics', name: 'get_topics', methods: ['GET'])]
    // TODO Ajouter un voter pour filtrer les topics par utilisateur si pas admin
    public function getAllTopics(
        Request $request,
        TopicRepository $topicRepository,
        PaginatorOptionsResolver $paginatorOptionsResolver
    ): JsonResponse {

        // Retrieve pagination parameters
        try {
            $queryParams = $paginatorOptionsResolver
                ->configurePage()
                ->resolve($request->query->all());
        } catch (Exception $e) {
            throw new ApiException($e->getMessage());
        }

        // Get data with pagination
        $topics = $topicRepository->findAllWithPagination($queryParams['page']);

        // Return paginate data
        return $this->json($topics);
    }

    #[Route('/topics/{id}', name: 'get_topic', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    // TODO Ajouter un voter pour filtrer les topics par utilisateur si pas admin
    public function getOneTopic(int $id, TopicRepository $topicRepository): JsonResponse
    {
        // Retrieve the element by id
        $topic = $topicRepository->find($id);

        // Check if the element exists
        if ($topic === null) {
            throw new ApiException("Topic with id $id was not found", Response::HTTP_NOT_FOUND);
        }

        return $this->json($topic);
    }

    #[Route('/topics', name: 'create_topic', methods: ['POST'])]
    public function createTopic(
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        TopicOptionsResolver $topicOptionsResolver,
        RequestPayloadService $requestPayloadService
    ): JsonResponse {

        try {
            // Retrieve the request body
            $body = $requestPayloadService->getRequestPayload($request);

            // Validate the content of the request body
            $data = $topicOptionsResolver
                ->configureName(true)
                ->resolve($body);
        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        // Temporarly create the element
        $topic = new Topic();
        $topic
            ->setName($data['name'])
            ->setAuthor($this->getUser());

        // Second validation using the validation constraints
        $errors = $validator->validate($topic, groups: ['Default']);
        if (count($errors) > 0) {
            throw new ApiException((string) $errors[0]->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        // Save the new element
        $em->persist($topic);
        $em->flush();

        // Return the element with the the status 201 (Created)
        return $this->json($topic, Response::HTTP_CREATED, [
            'Location' => $this->generateUrl('api_get_topic', ['id' => $topic->getId()]),
        ]);
    }

    #[Route('/topics/{id}', name: 'delete_topic', methods: ['DELETE'], requirements: ['id' => Regex::INTEGER])]
    public function deleteTopic(int $id, TopicRepository $topicRepository, EntityManagerInterface $em): JsonResponse
    {
        // Retrieve the element by id
        $topic = $topicRepository->find($id);

        // Check if the element exists
        if ($topic === null) {
            throw new ApiException("Topic with id $id was not found", Response::HTTP_NOT_FOUND);
        }

        // Remove the element
        $em->remove($topic);
        $em->flush();

        // Return a response with status 204 (No Content)
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/topics/{id}', name: 'update_topic', methods: ['PATCH', 'PUT'], requirements: ['id' => Regex::INTEGER])]
    public function updateTopic(
        int $id,
        TopicRepository $topicRepository,
        EntityManagerInterface $em,
        RequestPayloadService $requestPayloadService,
        Request $request,
        TopicOptionsResolver $flashcardOptionsResolver,
        ValidatorInterface $validator
    ): JsonResponse {

        // Retrieve the element by id
        $topic = $topicRepository->find($id);

        // Check if the element exists
        if ($topic === null) {
            throw new ApiException("Topic with id $id was not found", Response::HTTP_NOT_FOUND);
        }

        try {
            // Retrieve the request body
            $body = $requestPayloadService->getRequestPayload($request);

            // Check if the request method is PUT. In this case, all parameters must be provided in the request body.
            // Otherwise, all parameters are optional.
            $mandatoryParameters = $request->getMethod() === 'PUT';

            // Validate the content of the request body
            $data = $flashcardOptionsResolver
                ->configureName($mandatoryParameters)
                ->resolve($body);
        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        // Update each fields if necessary
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'name':
                    $topic->setName($value);
                    break;
                    // case 'author':
                    //     $flashcard->setAuthor($value);
                    //     break;
            }
        }

        // Second validation using the validation constraints
        $errors = $validator->validate($topic, groups: ['Default']);
        if (count($errors) > 0) {
            throw new ApiException((string) $errors[0]->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        // Save the element information
        $em->flush();

        // Return the element
        return $this->json($topic);
    }
}
