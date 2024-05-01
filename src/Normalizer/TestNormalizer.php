<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Model\Test;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TestNormalizer implements NormalizerInterface
{
    /**
     * @param Test $topic
     */
    public function normalize($topic, ?string $format = null, array $context = []): array
    {
        return [
            '@test' => 'Hello World!',
            'item' => $topic->string,
        ];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Test && $format === 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Test::class => true,
        ];
    }
}
