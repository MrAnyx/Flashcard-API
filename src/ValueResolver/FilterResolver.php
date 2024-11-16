<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Attribute\Searchable;
use App\Enum\OperatorType;
use App\Model\Filter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FilterResolver extends MapQueryStringRelativeToEntityResolver
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== Filter::class) {
            return [];
        }

        $relativeEntity = $this->getRelativeEntity($request, $argument);
        $searchableFields = $this->getFields($relativeEntity, Searchable::class);

        $filter = $request->query->get('filter');
        $value = $request->query->get('value');
        $operator = $request->query->getEnum('operator', OperatorType::class, OperatorType::EQUAL);

        if ($filter === null || $value === null) {
            return [null];
        }

        if (!\in_array($filter, $searchableFields)) {
            throw new BadRequestHttpException(\sprintf('Invalid "filter" parameter. Available options are: %s', implode(', ', $searchableFields)));
        }

        $pageInstance = new Filter($filter, $value, $operator);

        $errors = $this->validator->validate($pageInstance);
        if (\count($errors) > 0) {
            throw new BadRequestHttpException($errors[0]->getMessage());
        }

        yield $pageInstance;
    }
}
