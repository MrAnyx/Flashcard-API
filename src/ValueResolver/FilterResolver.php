<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Attribute\RelativeToEntity;
use App\Attribute\Searchable;
use App\Model\Filter;
use App\OptionsResolver\FilterOptionsResolver;
use App\Service\AttributeParser;
use App\Service\ObjectInitializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FilterResolver implements ValueResolverInterface
{
    public function __construct(
        private AttributeParser $attributeParser,
        private ObjectInitializer $objectInitializer,
        private FilterOptionsResolver $filterOptionsResolver,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== Filter::class) {
            return [];
        }

        $attribute = $argument->getAttributes(RelativeToEntity::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;

        if (!($attribute instanceof RelativeToEntity)) {
            throw new \InvalidArgumentException(\sprintf('Missing %s attribute', RelativeToEntity::class));
        }

        $reflectionProperties = $this->attributeParser->getFieldsWithAttribute($attribute->entity, Searchable::class);
        $searchableFields = array_map(fn (\ReflectionProperty $p) => $p->name, $reflectionProperties);

        try {
            $queryParams = $this->filterOptionsResolver
                ->configureOperator()
                ->configureFilter($searchableFields)
                ->configureValue($attribute->entity)
                ->setIgnoreUndefined()
                ->resolve($request->query->all());
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        try {
            return [
                $this->objectInitializer->initialize(Filter::class, $queryParams),
            ];
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, \sprintf('Can not initialize a %s object', Filter::class));
        }
    }
}
