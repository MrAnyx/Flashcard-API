<?php

declare(strict_types=1);

namespace App\Model;

readonly class ErrorStandard
{
    public function __construct(
        public string $message,
        public string $details,
        public ?array $trace = [],
    ) {
    }
}
