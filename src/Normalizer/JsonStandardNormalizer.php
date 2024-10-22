<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Model\JsonStandard;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class JsonStandardNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer,
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
            '@pagination' => $data->pagination,
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
        // Check if the data is an object
        if (\is_object($data)) {
            return $this->normalizer->normalize($data, $format, $context);
        }

        // Check if the data is an array
        if (\is_array($data)) {
            $normalizedArray = [];
            foreach ($data as $key => $value) {
                // Recursively normalize the value
                $normalizedArray[$key] = $this->normalizeData($value, $format, $context);
            }

            return $normalizedArray;
        }

        // If the data is scalar (string, int, float, bool, null), return as is
        if (\is_scalar($data) || $data === null) {
            return $data;
        }

        // If it's a type we didn't handle, throw an exception (optional)
        throw new \InvalidArgumentException('Unsupported data type for normalization');
    }
}
