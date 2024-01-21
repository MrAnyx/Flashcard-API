<?php

namespace App\Service;

use ReflectionClass;
use InvalidArgumentException;

class ObjectFactory
{
    /**
     * @template T
     *
     * @param class-string<T> $className
     * @param int $id
     * @return T
     */
    public static function create(string $fqcn, array $constructorArgs = []): object
    {
        if (! class_exists($fqcn)) {
            throw new InvalidArgumentException("Class $fqcn doesn't exist");
        }

        $reflectionClass = new ReflectionClass($fqcn);

        $instance = $reflectionClass->newInstanceArgs($constructorArgs);

        if ($instance === null) {
            throw new InvalidArgumentException("Error while creating the $fqcn object");
        }

        return $instance;
    }
}
