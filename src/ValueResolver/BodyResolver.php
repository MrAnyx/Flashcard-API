<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Attribute\Body;
use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class BodyResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $attribute = $argument->getAttributes(Body::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;

        if (!($attribute instanceof Body)) {
            return [];
        }

        $content = $request->getContent();

        if (!json_validate($content)) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, 'The request contains an invalid body that can not be parsed.');
        }

        return [
            json_decode($content, true),
        ];
    }
}
