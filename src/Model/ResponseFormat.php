<?php

declare(strict_types=1);

namespace App\Model;

class ResponseFormat
{
    public function __construct(
        public readonly string $format,
        public readonly string $mimeType,
    ) {
    }
}
