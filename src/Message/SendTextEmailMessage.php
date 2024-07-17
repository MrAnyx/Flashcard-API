<?php

declare(strict_types=1);

namespace App\Message;

final class SendTextEmailMessage
{
    public function __construct(
        public readonly string $email,
        public readonly string $username,
        public readonly int $priority,
        public readonly string $subject,
        public readonly string $message
    ) {
    }
}
