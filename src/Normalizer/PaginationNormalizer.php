<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Model\Pagination;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaginationNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    /**
     * @param Pagination $data
     */
    public function normalize($data, ?string $format = null, array $context = []): array
    {
        return $this->normalizer->normalize($data);
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Pagination;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Pagination::class => true,
        ];
    }
}
