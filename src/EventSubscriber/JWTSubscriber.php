<?php

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class JWTSubscriber implements EventSubscriberInterface
{
    public function onLexikJwtAuthenticationOnAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        // ...
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_authentication_failure' => 'onAuthenticationFailure',
            'lexik_jwt_authentication.on_jwt_invalid' => 'onJwtInvalid',
            'lexik_jwt_authentication.on_jwt_not_found' => 'onJwtNotFound',
            'lexik_jwt_authentication.on_jwt_expired' => 'onJwtExpired',
        ];
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        throw new UnauthorizedHttpException('Bearer', 'Invalid credentials');
    }

    public function onJwtInvalid(JWTInvalidEvent $event)
    {
        throw new UnauthorizedHttpException('Bearer', 'Invalid JWT token');
    }

    public function onJwtNotFound(JWTNotFoundEvent $event)
    {
        throw new UnauthorizedHttpException('Bearer', 'JWT token not found');
    }

    public function onJwtExpired(JWTExpiredEvent $event)
    {
        throw new UnauthorizedHttpException('Bearer', 'JWT token expired');
    }
}
