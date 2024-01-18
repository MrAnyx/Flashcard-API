<?php

namespace App\Security;

use App\Exception\ApiException;
use App\Exception\ExceptionCode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        throw new ApiException(Response::HTTP_UNAUTHORIZED, 'Invalid credentials', [], ExceptionCode::INVALID_CREDENTIALS);
    }
}
