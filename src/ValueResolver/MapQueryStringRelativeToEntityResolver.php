<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Attribute\RelativeToEntity;
use App\Service\AttributeParser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class MapQueryStringRelativeToEntityResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly AttributeParser $attributeParser,
        protected readonly ValidatorInterface $validator,
    ) {
    }

    protected function getRelativeEntity(Request $request, ArgumentMetadata $argument): string
    {
        $attribute = $argument->getAttributes(RelativeToEntity::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;

        if ($attribute instanceof RelativeToEntity) {
            return $attribute->entity;
        }

        // Retrieve controller and method from the request attributes
        [$controller, $method] = explode('::', $request->attributes->get('_controller'));

        // Validate the controller and method
        if (!class_exists($controller) || !method_exists($controller, $method)) {
            throw new \RuntimeException(\sprintf('Controller %s or method %s does not exist', $controller, $method));
        }

        // Reflection to get method and controller attributes
        $reflectionController = new \ReflectionClass($controller);
        $reflectionMethod = $reflectionController->getMethod($method);

        if (!empty($reflectionMethod->getAttributes(RelativeToEntity::class))) {
            return $reflectionMethod->getAttributes(RelativeToEntity::class)[0]->newInstance()->entity;
        } elseif (!empty($reflectionController->getAttributes(RelativeToEntity::class))) {
            return $reflectionController->getAttributes(RelativeToEntity::class)[0]->newInstance()->entity;
        }

        throw new \RuntimeException(\sprintf('Missing attribute %s on parameter, method, or controller', RelativeToEntity::class));
    }

    protected function getFields(string $relativeEntity, string $attribute)
    {
        $reflectionProperties = $this->attributeParser->getFieldsWithAttribute($relativeEntity, $attribute);

        return array_map(fn (\ReflectionProperty $p) => $p->name, $reflectionProperties);
    }
}
