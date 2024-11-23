<?php

declare(strict_types=1);

namespace App\Security;

use App\Exception\Http\UnauthorizedHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function __construct(
        private readonly RateLimiterFactory $loginLimiter,
    ) {
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $limiter = $this->loginLimiter->create($request->getClientIp());

        $rateLimit = $limiter->consume(1);

        if (!$rateLimit->isAccepted()) {
            throw new HttpException(Response::HTTP_TOO_MANY_REQUESTS, 'Too many login attempts. Please try again later');
        }

        throw new UnauthorizedHttpException('Invalid credentials', $exception);
    }
}
