<?php

namespace App\Controller;

use Exception;
use App\Service\EntityChecker;
use App\Exception\ApiException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\OptionsResolver\PaginatorOptionsResolver;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AbstractRestController extends AbstractController
{
    public function __construct(
        private EntityChecker $entityChecker,
        private PaginatorOptionsResolver $paginatorOptionsResolver,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em
    ) {
    }

    /**
     * @param string $classname This classname is used to retrieve the sortable fields
     * @param Request $request Request to retrieve the query parameters
     */
    public function getPaginationParameter(string $classname, Request $request): array
    {
        $sortableFields = $this->entityChecker->getSortableFields($classname);

        try {
            $queryParams = $this->paginatorOptionsResolver
                ->configurePage()
                ->configureSort($sortableFields)
                ->configureOrder()
                ->resolve($request->query->all());
        } catch (Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        return $queryParams;
    }

    /**
     * @param mixed $entity Entity to validate
     * @param array $validationGroups Validation groups (default: ["Default"])
     */
    public function validateEntity(mixed $entity, array $validationGroups = ['Default']): void
    {
        $errors = $this->validator->validate($entity, groups: $validationGroups);
        if (count($errors) > 0) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, (string) $errors[0]->getMessage());
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
            throw new ApiException(Response::HTTP_NOT_FOUND, 'Resource with id %d was not found', [$id]);
        }

        return $resource;
    }
}
