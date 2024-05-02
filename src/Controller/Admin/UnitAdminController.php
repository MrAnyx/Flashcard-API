<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractRestController;
use App\Entity\Unit;
use App\Exception\ApiException;
use App\OptionsResolver\UnitOptionsResolver;
use App\Repository\UnitRepository;
use App\Service\RequestPayloadService;
use App\Utility\Regex;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin', 'api_admin_', format: 'json')]
class UnitAdminController extends AbstractRestController
{
    #[Route('/units', name: 'get_units', methods: ['GET'])]
    public function getAllUnits(
        Request $request,
        UnitRepository $unitRepository
    ): JsonResponse {
        $pagination = $this->getPaginationParameter(Unit::class, $request);

        // Get data with pagination
        $units = $unitRepository->findAllWithPagination(
            $pagination->page,
            $pagination->sort,
            $pagination->order
        );

        // Return paginate data
        return $this->jsonStd($units, context: ['groups' => ['read:unit:admin']]);
    }

    #[Route('/units/{id}', name: 'get_unit', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    public function getOneUnit(int $id, UnitRepository $unitRepository): JsonResponse
    {
        // Retrieve the element by id
        $unit = $unitRepository->find($id);

        // Check if the element exists
        if ($unit === null) {
            throw new ApiException(Response::HTTP_NOT_FOUND, 'Unit with id %d was not found', [$id]);
        }

        return $this->jsonStd($unit, context: ['groups' => ['read:unit:admin']]);
    }

    #[Route('/units', name: 'create_unit', methods: ['POST'])]
    public function createUnit(
        Request $request,
        EntityManagerInterface $em,
        UnitOptionsResolver $unitOptionsResolver,
        RequestPayloadService $requestPayloadService
    ): JsonResponse {
        try {
            // Retrieve the request body
            $body = $requestPayloadService->getRequestPayload($request);

            // Validate the content of the request body
            $data = $unitOptionsResolver
                ->configureName(true)
                ->configureTopic(true)
                ->configureDescription(true)
                ->configureFavorite(true)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

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
            ['Location' => $this->generateUrl('api_admin_get_unit', ['id' => $unit->getId()])],
            ['groups' => ['read:unit:admin']]
        );
    }

    #[Route('/units/{id}', name: 'delete_unit', methods: ['DELETE'], requirements: ['id' => Regex::INTEGER])]
    public function deleteUnit(int $id, UnitRepository $unitRepository, EntityManagerInterface $em): JsonResponse
    {
        // Retrieve the element by id
        $unit = $unitRepository->find($id);

        // Check if the element exists
        if ($unit === null) {
            throw new ApiException(Response::HTTP_NOT_FOUND, 'Unit with id %d was not found', [$id]);
        }

        // Remove the element
        $em->remove($unit);
        $em->flush();

        // Return a response with status 204 (No Content)
        return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/units/{id}', name: 'update_unit', methods: ['PATCH', 'PUT'], requirements: ['id' => Regex::INTEGER])]
    public function updateUnit(
        int $id,
        UnitRepository $unitRepository,
        EntityManagerInterface $em,
        RequestPayloadService $requestPayloadService,
        Request $request,
        UnitOptionsResolver $unitOptionsResolver,
    ): JsonResponse {
        // Retrieve the element by id
        $unit = $unitRepository->find($id);

        // Check if the element exists
        if ($unit === null) {
            throw new ApiException(Response::HTTP_NOT_FOUND, 'Unit with id %d was not found', [$id]);
        }

        try {
            // Retrieve the request body
            $body = $requestPayloadService->getRequestPayload($request);

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
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        // Update each fields if necessary
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'name':
                    $unit->setName($value);
                    break;
                case 'topic':
                    $unit->setTopic($value);
                    break;
                case 'description':
                    $unit->setDescription($value);
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
        return $this->jsonStd($unit, context: ['groups' => ['read:unit:admin']]);
    }
}
