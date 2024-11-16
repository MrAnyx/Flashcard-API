<?php

declare(strict_types=1);

namespace App\Service;

use App\Transformer\Transformer;
use App\Transformer\TransformerInterface;
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
     * @param array<string, Transformer[]> $transformers
     * @param array<string, Transformer[]> $mutators
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
        array $validationGroups = ['Default'],
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

        foreach ($transformedBody as $field => $value) {
            if (\array_key_exists($field, $transformers)) {
                foreach ($transformers[$field] as $transformer) {
                    /** @var TransformerInterface $transformerInstance */
                    $transformerInstance = $this->container->get($transformer->transformerClassname);

                    $transformedBody[$field] = $transformerInstance->transform($transformedBody[$field], $transformer->context);
                }
            }
        }

        $entity = $fromObject ?? new $classname();
        foreach ($transformedBody as $field => $value) {
            $this->propertyAccessor->setValue($entity, $field, $value);
        }

        foreach ($transformedBody as $field => $value) {
            if (\array_key_exists($field, $mutators)) {
                foreach ($mutators[$field] as $mutator) {
                    $currentValue = $this->propertyAccessor->getValue($entity, $field);

                    /** @var TransformerInterface $mutatorInstance */
                    $mutatorInstance = $this->container->get($mutator->transformerClassname);

                    $this->propertyAccessor->setValue($entity, $field, $mutatorInstance->transform($currentValue, $mutator->context));
                }
            }
        }

        $errors = $this->validator->validate($entity, groups: $validationGroups);
        if (\count($errors) > 0) {
            throw new \Exception((string) $errors[0]->getMessage());
        }

        return $entity;
    }
}
