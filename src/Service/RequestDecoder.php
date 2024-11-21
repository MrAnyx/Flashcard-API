<?php

declare(strict_types=1);

namespace App\Service;

use App\Modifier\Modifier;
use App\Modifier\Mutator\MutatorInterface;
use App\Modifier\Transformer\TransformerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\SerializerExtractor;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDecoder
{
    private readonly SerializerExtractor $serializerExtractor;

    private readonly PropertyAccessor $propertyAccessor;

    public function __construct(
        private readonly ValidatorInterface $validator,
        #[Autowire(service: 'service_container')]
        private readonly ContainerInterface $container,
    ) {
        $serializerClassMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $this->serializerExtractor = new SerializerExtractor($serializerClassMetadataFactory);

        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @template T
     *
     * @param class-string<T> $classname
     * @param bool|null $strict Define the strictness of the field resolution. With value null, the strictness with be guessed by the request method (POST and PUT)
     * @param Modifier[] $transformers
     * @param Modifier[] $mutators
     *
     * @return T
     */
    public function decode(
        string $classname,
        ?object $fromObject = null,
        ?bool $strict = null,
        array $deserializationGroups = [],
        bool $ignoreUnknownFields = true,
        array $transformers = [],
        array $mutators = [],
        ?array $validationGroups = ['Default'],
    ): object {
        $request = Request::createFromGlobals();
        $method = $request->getMethod();

        $requestBody = $request->toArray();

        $allFieldsmandatory = $strict ?? \in_array($method, [Request::METHOD_POST, Request::METHOD_PUT]);

        $writableFields = $this->serializerExtractor->getProperties($classname, ['serializer_groups' => $deserializationGroups]);

        $fieldsOptionsResolver = (new OptionsResolver())->setIgnoreUndefined($ignoreUnknownFields);

        foreach ($writableFields as $field) {
            $fieldsOptionsResolver->setDefined($field);

            if ($allFieldsmandatory) {
                $fieldsOptionsResolver->setRequired($field);
            }
        }

        $transformedBody = $fieldsOptionsResolver->resolve($requestBody);

        foreach ($transformers as $transformer) {
            if (!\array_key_exists($transformer->field, $transformedBody)) {
                continue;
            }

            if (!is_subclass_of($transformer->modifierClassname, TransformerInterface::class)) {
                throw new \InvalidArgumentException(\sprintf('Transformer %s must implement the %s interface', $transformer->modifierClassname, TransformerInterface::class));
            }

            /** @var TransformerInterface $transformerInstance */
            $transformerInstance = $this->container->get($transformer->modifierClassname);
            $transformedBody[$transformer->field] = $transformerInstance->transform($transformedBody[$transformer->field], $transformer->context);
        }

        $entity = $fromObject ?? new $classname();
        foreach ($transformedBody as $field => $value) {
            $this->propertyAccessor->setValue($entity, $field, $value);
        }

        foreach ($mutators as $mutator) {
            if (!\array_key_exists($mutator->field, $transformedBody)) {
                continue;
            }

            if (!is_subclass_of($mutator->modifierClassname, MutatorInterface::class)) {
                throw new \InvalidArgumentException(\sprintf('Mutator %s must implement the %s interface', $mutator->modifierClassname, MutatorInterface::class));
            }

            $currentValue = $this->propertyAccessor->getValue($entity, $mutator->field);

            /** @var MutatorInterface $mutatorInstance */
            $mutatorInstance = $this->container->get($mutator->modifierClassname);
            $mutatorInstance->mutate($entity, $currentValue, $mutator->context);
        }

        if ($validationGroups !== null) {
            $errors = $this->validator->validate($entity, groups: $validationGroups);
            if (\count($errors) > 0) {
                throw new \Exception((string) $errors[0]->getMessage());
            }
        }

        return $entity;
    }
}
