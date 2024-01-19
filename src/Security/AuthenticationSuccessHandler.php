<?php

namespace App\Security;

use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        /** @var \App\Entity\User | null $user */
        $user = $token->getUser();

        if ($user === null) {
            throw new ApiException(Response::HTTP_UNAUTHORIZED, 'Unauthenticated user');
        }

        $userApiToken = $user->getToken();

        return new JsonResponse(['token' => $userApiToken]);
    }
}
