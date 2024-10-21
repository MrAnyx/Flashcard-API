<?php

declare(strict_types=1);

namespace App\Service;

class ObjectInitializer
{
    /**
     * @template T
     *
     * @param class-string<T> $classname
     *
     * @return ?T
     */
    public static function initialize(string $classname, array $constructorArgs = [])
    {
        if (!class_exists($classname)) {
            throw new \RuntimeException("Class {$classname} does not exist.");
        }

        $converter = new \ReflectionClass($classname);
        $constructor = $converter->getConstructor();

        $constructorParams = [];

        if ($constructor) {
            $constructorReflectioParams = $constructor->getParameters();
            $constructorParams = self::mapOptionsToConstructorArgs($constructorReflectioParams, $constructorArgs);
        }

        return $converter->newInstanceArgs($constructorParams);
    }

    /**
     * @param \ReflectionParameter[] $constructorParams
     */
    private static function mapOptionsToConstructorArgs(array $constructorParams, array $options): array
    {
        $constructorArgs = [];

        foreach ($constructorParams as $param) {
            $paramName = $param->getName();

            if (\array_key_exists($paramName, $options)) {
                $constructorArgs[] = $options[$paramName];
            } elseif ($param->isOptional()) {
                $constructorArgs[] = $param->getDefaultValue();
            } else {
                throw new \RuntimeException("Missing required option for parameter '{$paramName}'.");
            }
        }

        return $constructorArgs;
    }
}
