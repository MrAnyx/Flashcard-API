<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Attribute\RelativeToEntity;
use App\Attribute\Sortable;
use App\Exception\ApiException;
use App\Model\Page;
use App\OptionsResolver\PaginatorOptionsResolver;
use App\Service\AttributeParser;
use App\Service\ObjectInitializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class PageResolver implements ValueResolverInterface
{
    public function __construct(
        private AttributeParser $attributeParser,
        private ObjectInitializer $objectInitializer,
        private PaginatorOptionsResolver $paginatorOptionsResolver,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== Page::class) {
            return [];
        }

        $attribute = $argument->getAttributes(RelativeToEntity::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;

        if (!($attribute instanceof RelativeToEntity)) {
            throw new \InvalidArgumentException(\sprintf('Missing %s attribute', RelativeToEntity::class));
        }

        $reflectionProperties = $this->attributeParser->getFieldsWithAttribute($attribute->entity, Sortable::class);
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
            return [
                $this->objectInitializer->initialize(Page::class, $queryParams),
            ];
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Can not initialize a %s object', [Page::class]);
        }
    }
}
