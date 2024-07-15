<?php

declare(strict_types=1);

namespace App\UniqueGenerator;

class UniqueTokenGenerator extends AbstractUniqueGenerator
{
    public const MAX_LENGTH = 36;

    protected function generateValue(int $iteration): string
    {
        return bin2hex(random_bytes(self::MAX_LENGTH));
    }
}
