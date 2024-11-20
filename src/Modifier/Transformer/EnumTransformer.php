<?php

declare(strict_types=1);

namespace App\Modifier\Transformer;

class EnumTransformer implements TransformerInterface
{
    public function transform(mixed $rawValue, array $context): mixed
    {
        $enumClass = $context['enum'] ?? throw new \InvalidArgumentException(\sprintf('Missing key "%s" in context', 'enum'));

        if (!is_subclass_of($enumClass, \BackedEnum::class)) {
            throw new \InvalidArgumentException(\sprintf('Class %s is not a valid backed enum', $enumClass));
        }

        return $enumClass::tryFrom($rawValue) ?? throw new \InvalidArgumentException(\sprintf('Unable to convert value "%s" into enum of type %s', $rawValue, $enumClass));
    }
}
