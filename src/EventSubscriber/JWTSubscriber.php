<?php

namespace App\EventSubscriber;

use App\Exception\ApiException;
use App\Exception\ExceptionStatus;
use App\Exception\ExceptionMessage;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Gesdinet\JWTRefreshTokenBundle\Event\RefreshTokenNotFoundEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
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
        throw new UnauthorizedHttpException('Bearer', 'Invalid credentials');
        // TODO A terminer
    }

    public function onJwtInvalid(JWTInvalidEvent $event)
    {
        throw new UnauthorizedHttpException('Bearer', 'Invalid JWT token');
        // TODO A terminer
    }

    public function onJwtNotFound(JWTNotFoundEvent $event)
    {
        throw new ApiException(
            ExceptionMessage::JWT_TOKEN_NOT_FOUND,
            ExceptionStatus::NOT_FOUND
        );
    }

    public function onJwtExpired(JWTExpiredEvent $event)
    {
        // throw new UnauthorizedHttpException('Bearer', 'JWT token expired');
        throw new ApiException(
            ExceptionMessage::JWT_TOKEN_EXPIRED,
            ExceptionStatus::UNAUTHORIZED
        );
    }

    public function onJwtRefreshTokenFailure(RefreshAuthenticationFailureEvent $event)
    {
        throw new ApiException(
            ExceptionMessage::JWT_REFRESH_TOKEN_NOT_FOUND,
            ExceptionStatus::NOT_FOUND
        );
    }

    public function onJwtRefreshTokenNotFound(RefreshTokenNotFoundEvent $event)
    {
        throw new ApiException(
            ExceptionMessage::JWT_REFRESH_TOKEN_FAILURE,
            ExceptionStatus::INTERNAL_SERVER_ERROR
        );
    }
}
