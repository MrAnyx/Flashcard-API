<?php

namespace App\Controller\Admin;

use Exception;
use App\Entity\Topic;
use App\Utility\Regex;
use App\Exception\ApiException;
use App\Exception\ExceptionCode;
use App\Repository\TopicRepository;
use App\Service\RequestPayloadService;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\AbstractRestController;
use App\OptionsResolver\TopicOptionsResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/admin', 'api_admin_', format: 'json')]
class TopicAdminController extends AbstractRestController
{
    #[Route('/topics', name: 'get_topics', methods: ['GET'])]
    public function getAllTopics(
        Request $request,
        TopicRepository $topicRepository
    ): JsonResponse {

        $pagination = $this->getPaginationParameter(Topic::class, $request);

        // Get data with pagination
        $topics = $topicRepository->findAllWithPagination(
            $pagination['page'],
            $pagination['sort'],
            $pagination['order']
        );

        // Return paginate data
        return $this->json($topics, context: ['groups' => ['read:topic:admin']]);
    }

    #[Route('/topics/{id}', name: 'get_topic', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getOneTopic(int $id, TopicRepository $topicRepository): JsonResponse
    {
        // Retrieve the element by id
        $topic = $topicRepository->find($id);

        // Check if the element exists
        if ($topic === null) {
            throw new ApiException(Response::HTTP_NOT_FOUND, 'Topic with id %d was not found', [$id], ExceptionCode::RESOURCE_NOT_FOUND);
        }

        return $this->json($topic, context: ['groups' => ['read:topic:admin']]);
    }

    #[Route('/topics', name: 'create_topic', methods: ['POST'])]
    public function createTopic(
        Request $request,
        EntityManagerInterface $em,
        TopicOptionsResolver $topicOptionsResolver,
        RequestPayloadService $requestPayloadService
    ): JsonResponse {

        try {
            // Retrieve the request body
            $body = $requestPayloadService->getRequestPayload($request);

            // Validate the content of the request body
            $data = $topicOptionsResolver
                ->configureName(true)
                ->configureAuthor(true)
                ->resolve($body);
        } catch (Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage(), [], ExceptionCode::INVALID_REQUEST_BODY);
        }

        // Temporarly create the element
        $topic = new Topic();
        $topic
            ->setName($data['name'])
            ->setAuthor($data['author']);

        // Second validation using the validation constraints
        $this->validateEntity($topic);

        // Save the new element
        $em->persist($topic);
        $em->flush();

        // Return the element with the the status 201 (Created)
        return $this->json(
            $topic,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('api_admin_get_topic', ['id' => $topic->getId()])],
            ['groups' => ['read:topic:admin']]
        );
    }

    #[Route('/topics/{id}', name: 'delete_topic', methods: ['DELETE'], requirements: ['id' => Regex::INTEGER])]
    public function deleteTopic(int $id, TopicRepository $topicRepository, EntityManagerInterface $em): JsonResponse
    {
        // Retrieve the element by id
        $topic = $topicRepository->find($id);

        // Check if the element exists
        if ($topic === null) {
            throw new ApiException(Response::HTTP_NOT_FOUND, 'Topic with id %d was not found', [$id], ExceptionCode::RESOURCE_NOT_FOUND);
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
    ): JsonResponse {

        // Retrieve the element by id
        $topic = $topicRepository->find($id);

        // Check if the element exists
        if ($topic === null) {
            throw new ApiException(Response::HTTP_NOT_FOUND, 'Topic with id %d was not found', [$id], ExceptionCode::RESOURCE_NOT_FOUND);
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
                ->configureAuthor($mandatoryParameters)
                ->resolve($body);
        } catch (Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage(), [], ExceptionCode::INVALID_REQUEST_BODY);
        }

        // Update each fields if necessary
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'name':
                    $topic->setName($value);
                    break;
                case 'author':
                    $topic->setAuthor($value);
                    break;
            }
        }

        // Second validation using the validation constraints
        $this->validateEntity($topic);

        // Save the element information
        $em->flush();

        // Return the element
        return $this->json($topic, context: ['groups' => ['read:topic:admin']]);
    }
}
