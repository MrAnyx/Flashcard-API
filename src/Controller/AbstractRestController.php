<?php

namespace App\Controller;

use Exception;
use App\Exception\ApiException;
use App\Exception\ExceptionCode;
use App\Service\EntityValidator;
use App\Service\SortableEntityChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\OptionsResolver\PaginatorOptionsResolver;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AbstractRestController extends AbstractController
{
    public function __construct(
        private SortableEntityChecker $sortableEntityChecker,
        private PaginatorOptionsResolver $paginatorOptionsResolver,
        private EntityManagerInterface $em,
        private EntityValidator $entityValidator
    ) {
    }

    /**
     * @param string $classname This classname is used to retrieve the sortable fields
     * @param Request $request Request to retrieve the query parameters
     */
    public function getPaginationParameter(string $classname, Request $request): array
    {
        $sortableFields = $this->sortableEntityChecker->getSortableFields($classname);

        try {
            $queryParams = $this->paginatorOptionsResolver
                ->configurePage()
                ->configureSort($sortableFields)
                ->configureOrder()
                ->resolve($request->query->all());
        } catch (Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage(), [], ExceptionCode::INVALID_PAGINATION);
        }

        return $queryParams;
    }

    public function validateEntity(mixed $entity, array $validationGroups = ['Default']): void
    {
        try {
            $this->entityValidator->validateEntity($entity, $validationGroups);
        } catch (ValidatorException $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage(), [], ExceptionCode::VALIDATION_FAILURE);
        }
    }

    /**
     * @template T
     *
     * @param class-string<T> $classname
     * @return T
     */
    public function getResourceById(string $classname, int $id): mixed
    {
        // Retrieve the resource by id
        $resource = $this->em->find($classname, $id);

        // Check if the flashcard exists
        if ($resource === null) {
            throw new ApiException(Response::HTTP_NOT_FOUND, 'Resource with id %d was not found', [$id], ExceptionCode::RESOURCE_NOT_FOUND);
        }

        return $resource;
    }
}
