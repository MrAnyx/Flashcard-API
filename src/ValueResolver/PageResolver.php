<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Attribute\Sortable;
use App\Model\Page;
use Doctrine\Common\Collections\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PageResolver extends MapQueryStringRelativeToEntityResolver
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== Page::class) {
            return [];
        }

        $relativeEntity = $this->getRelativeEntity($request, $argument);
        $sortableFields = $this->getFields($relativeEntity, Sortable::class);

        $page = $request->query->getInt('page', 1);
        $sort = $request->query->getString('sort', 'id');
        $strOrder = mb_strtoupper($request->query->getString('order', Order::Ascending->value));
        $itemsPerPage = $request->query->getInt('itemsPerPage', 25);

        if (!\in_array($sort, $sortableFields)) {
            throw new BadRequestHttpException(\sprintf('Invalid "sort" parameter. Available options are: %s', implode(', ', $sortableFields)));
        }

        $order = Order::tryFrom($strOrder) ?? throw new BadRequestHttpException('Invalid "order" parameter');

        $pageInstance = new Page($page, $sort, $order, $itemsPerPage);

        $errors = $this->validator->validate($pageInstance);

        if (\count($errors) > 0) {
            throw new BadRequestHttpException($errors[0]->getMessage());
        }

        return [$pageInstance];
    }
}
