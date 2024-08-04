<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribut\Searchable;
use App\Attribut\Sortable;
use App\Enum\JsonStandardStatus;
use App\Exception\ApiException;
use App\Model\Filter;
use App\Model\JsonStandard;
use App\Model\Page;
use App\Model\TypedField;
use App\OptionsResolver\FilterOptionsResolver;
use App\OptionsResolver\PaginatorOptionsResolver;
use App\Service\AttributeHelper;
use App\Service\EntityValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class AbstractRestController extends AbstractController
{
    public function __construct(
        private AttributeHelper $attributeHelper,
        private PaginatorOptionsResolver $paginatorOptionsResolver,
        private FilterOptionsResolver $filterOptionsResolver,
        private EntityManagerInterface $em,
        private EntityValidator $entityValidator,
        private DenormalizerInterface $denormalizer
    ) {
    }

    /**
     * @param string $classname This classname is used to retrieve the sortable fields
     * @param Request $request Request to retrieve the query parameters
     */
    public function getPaginationParameter(string $classname, Request $request): Page
    {
        $sortableFields = $this->attributeHelper->getFieldsWithAttribute($classname, Sortable::class);
        $sortableFieldNames = array_map(fn (TypedField $field) => $field->name, $sortableFields);

        try {
            $queryParams = $this->paginatorOptionsResolver
                ->configurePage()
                ->configureSort($sortableFieldNames)
                ->configureOrder()
                ->configureItemsPerPage()
                ->setIgnoreUndefined(true)
                ->resolve($request->query->all());
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        try {
            $page = $this->denormalizer->denormalize($queryParams, Page::class);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'An error occured');
        }

        return $page;
    }

    /**
     * @param string $classname This classname is used to retrieve the sortable fields
     * @param Request $request Request to retrieve the query parameters
     */
    public function getFilterParameter(string $classname, Request $request): Filter
    {
        $searchableFields = $this->attributeHelper->getFieldsWithAttribute($classname, Searchable::class);
        $searchableFieldNames = array_map(fn (TypedField $field) => $field->name, $searchableFields);

        try {
            $queryParams = $this->filterOptionsResolver
                ->configureQuery($searchableFields)
                ->configureFilter($searchableFieldNames)
                ->setIgnoreUndefined(true)
                ->resolve($request->query->all());
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        try {
            $filter = $this->denormalizer->denormalize($queryParams, Filter::class);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'An error occured');
        }

        return $filter;
    }

    public function validateEntity(mixed $entity, array $validationGroups = ['Default']): void
    {
        try {
            $this->entityValidator->validateEntity($entity, $validationGroups);
        } catch (ValidatorException $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    /**
     * @template T
     *
     * @param class-string<T> $classname
     *
     * @return T
     */
    public function getResourceById(string $classname, int $id): mixed
    {
        // Retrieve the resource by id
        $resource = $this->em->find($classname, $id);

        // Check if the flashcard exists
        if ($resource === null) {
            throw new ApiException(Response::HTTP_NOT_FOUND, 'Resource with id %d was not found', [$id]);
        }

        return $resource;
    }

    public function jsonStd(mixed $data, int $status = 200, array $headers = [], array $context = [], JsonStandardStatus $jsonStatus = JsonStandardStatus::VALID): JsonResponse
    {
        return $this->json(new JsonStandard($data, $jsonStatus), $status, $headers, $context);
    }
}
