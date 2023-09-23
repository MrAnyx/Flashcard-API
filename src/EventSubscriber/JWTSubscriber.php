<?php

namespace App\EventSubscriber;

use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Gesdinet\JWTRefreshTokenBundle\Event\RefreshTokenNotFoundEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Gesdinet\JWTRefreshTokenBundle\Event\RefreshAuthenticationFailureEvent;

/**
 * This subscriber allows the application to return custom exception, not the json ExceptionStatus from the bundle that doesn't contain the required error fields
 */
class JWTSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_authentication_failure' => 'onAuthenticationFailure',
            'lexik_jwt_authentication.on_jwt_invalid' => 'onJwtInvalid',
            'lexik_jwt_authentication.on_jwt_not_found' => 'onJwtNotFound',
            'lexik_jwt_authentication.on_jwt_expired' => 'onJwtExpired',
            'gesdinet.refresh_token_failure' => 'onJwtRefreshTokenFailure',
            'gesdinet.refresh_token_not_found' => 'onJwtRefreshTokenNotFound',
        ];
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        throw new ApiException(
            Response::HTTP_UNAUTHORIZED,
            'Invalid credentials',
        );
    }

    public function onJwtInvalid(JWTInvalidEvent $event)
    {
        throw new ApiException(
            Response::HTTP_UNAUTHORIZED,
            'Invalid JWT token. You must provide a valid JWT token for this request',
        );
    }

    public function onJwtNotFound(JWTNotFoundEvent $event)
    {
        throw new ApiException(
            Response::HTTP_UNAUTHORIZED,
            'JWT token not found. Please, provide a valid JWT token using the Bearer authentication method',
        );
    }

    public function onJwtExpired(JWTExpiredEvent $event)
    {
        throw new ApiException(
            Response::HTTP_UNAUTHORIZED,
            'JWT token has expired. Please, login again or refresh your token using your refresh token',
        );
    }

    public function onJwtRefreshTokenFailure(RefreshAuthenticationFailureEvent $event)
    {
        throw new ApiException(
            Response::HTTP_BAD_REQUEST,
            'An error occured when trying to refresh you JWT token. Please try again later',
        );
    }

    public function onJwtRefreshTokenNotFound(RefreshTokenNotFoundEvent $event)
    {
        throw new ApiException(
            Response::HTTP_NOT_FOUND,
            'JWT refresh token not found. Please, you must provide a valid JWT refresh token in order to refresh your existing token',
        );
    }
}
