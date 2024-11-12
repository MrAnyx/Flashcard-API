<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Attribute\Body;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BodyResolver implements ValueResolverInterface
{
    public function __construct(
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $bodyAttribute = $argument->getAttributes(Body::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;

        if (!($bodyAttribute instanceof Body)) {
            return [];
        }

        $isValidMethod = "is_{$bodyAttribute->expectedType}";

        if (!\function_exists($isValidMethod)) {
            throw new \InvalidArgumentException(\sprintf('Function %s doesn \'t exist', $isValidMethod));
        }

        if (!json_validate($request->getContent())) {
            throw new BadRequestHttpException('Request body contains invalid json');
        }

        $body = json_decode($request->getContent(), true);

        if (!$isValidMethod($body)) {
            throw new BadRequestHttpException(\sprintf('Request body must be of type %s, %s given', $bodyAttribute->expectedType, \gettype($body)));
        }

        return [
            $body,
        ];
    }
}
