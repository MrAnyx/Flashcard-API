<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;

class TokenGenerator
{
    public const LENGTH = 36;

    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function generateToken(int $length = self::LENGTH)
    {
        do {
            $token = bin2hex(random_bytes($length));
            $userExists = $this->userRepository->findOneBy(['token' => $token]) !== null;
        } while ($userExists === true);

        return $token;
    }
}
