<?php

declare(strict_types=1);

namespace App\DependencyInjection;

use App\Modifier\Mutator\MutatorInterface;
use App\Modifier\Transformer\TransformerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TransformerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            $class = $definition->getClass();

            // Skip services without a class
            if (null === $class) {
                continue;
            }

            // Ensure the class implements TransformerInterface or MutatorInterface
            if (is_subclass_of($class, TransformerInterface::class) || is_subclass_of($class, MutatorInterface::class)) {
                $definition->setPublic(true);
            }
        }
    }
}
