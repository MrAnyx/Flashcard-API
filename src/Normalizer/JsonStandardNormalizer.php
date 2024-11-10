<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Model\JsonStandard;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class JsonStandardNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: ObjectNormalizer::class)]
        private readonly NormalizerInterface $objectNormalizer,
    ) {
    }

    /**
     * @param JsonStandard $data
     */
    public function normalize($data, ?string $format = null, array $context = []): array
    {
        return [
            '@timestamp' => $data->timestamp->format(\DateTimeImmutable::ATOM),
            '@status' => $data->status,
            '@pagination' => $data->pagination ? $this->objectNormalizer->normalize($data->pagination, $format) : null,
            'data' => $this->normalizeData($data->data, $format, $context),
        ];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof JsonStandard;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            JsonStandard::class => true,
        ];
    }

    private function normalizeData($data, ?string $format = null, array $context = []): mixed
    {
        // If the data is scalar (string, int, float, bool, null), return as is
        if (\is_scalar($data) || $data === null) {
            return $data;
        }

        // Check if the data is an object
        if (\is_object($data)) {
            return $this->objectNormalizer->normalize($data, $format, $context);
        }

        // Check if the data is an array
        if (\is_array($data)) {
            return array_map(fn ($item) => $this->normalizeData($item, $format, $context), $data);
        }

        // If it's a type we didn't handle, throw an exception (optional)
        throw new \InvalidArgumentException('Unsupported data type for normalization');
    }
}
