<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\Searchable;
use App\Attribute\Sortable;
use App\Entity\User;
use App\Enum\JsonStandardStatus;
use App\Enum\SettingName;
use App\Exception\ApiException;
use App\Model\Filter;
use App\Model\JsonStandard;
use App\Model\Page;
use App\OptionsResolver\FilterOptionsResolver;
use App\OptionsResolver\PaginatorOptionsResolver;
use App\Service\AttributeParser;
use App\Service\ObjectInitializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractRestController extends AbstractController
{
    public function __construct(
        private AttributeParser $attributeParser,
        private PaginatorOptionsResolver $paginatorOptionsResolver,
        private FilterOptionsResolver $filterOptionsResolver,
        private EntityManagerInterface $em,
        // private DenormalizerInterface $denormalizer,
        private ValidatorInterface $validator,
        private ObjectInitializer $objectInitializer,
    ) {
    }

    /**
     * @param string $classname This classname is used to retrieve the sortable fields
     * @param Request $request Request to retrieve the query parameters
     */
    public function getPaginationParameter(string $classname, Request $request): Page
    {
        $reflectionProperties = $this->attributeParser->getFieldsWithAttribute($classname, Sortable::class);
        $sortableFields = array_map(fn (\ReflectionProperty $p) => $p->name, $reflectionProperties);

        try {
            $queryParams = $this->paginatorOptionsResolver
                ->configurePage()
                ->configureSort($sortableFields)
                ->configureOrder()
                ->configureItemsPerPage()
                ->setIgnoreUndefined()
                ->resolve($request->query->all());
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        try {
            return $this->objectInitializer->initialize(Page::class, $queryParams);
            // return $this->denormalizer->denormalize($queryParams, Page::class);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'An error occured');
        }
    }

    /**
     * @param string $classname This classname is used to retrieve the filter fields
     * @param Request $request Request to retrieve the query parameters
     */
    public function getFilterParameter(string $classname, Request $request): Filter
    {
        $reflectionProperties = $this->attributeParser->getFieldsWithAttribute($classname, Searchable::class);
        $searchableFields = array_map(fn (\ReflectionProperty $p) => $p->name, $reflectionProperties);

        try {
            $queryParams = $this->filterOptionsResolver
                ->configureOperator()
                ->configureFilter($searchableFields)
                ->configureValue($classname)
                ->setIgnoreUndefined()
                ->resolve($request->query->all());
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        try {
            // return $this->denormalizer->denormalize($queryParams, Filter::class);
            return $this->objectInitializer->initialize(Filter::class, $queryParams);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'An error occured');
        }
    }

    public function validateEntity(mixed $entity, array $validationGroups = ['Default']): void
    {
        $errors = $this->validator->validate($entity, groups: $validationGroups);
        if (\count($errors) > 0) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, (string) $errors[0]->getMessage());
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

    public function getRequestPayload(Request $request): mixed
    {
        if (!json_validate($request->getContent())) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, 'The request contains an invalid body that can not be parsed. Please verify the json body of the request.');
        }

        return json_decode($request->getContent(), true);
    }

    public function getUserSetting(SettingName $settingName): mixed
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        return $user->getSetting($settingName);
    }
}
