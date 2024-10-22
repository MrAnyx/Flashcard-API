<?php

declare(strict_types=1);

namespace App\Voter;

use App\Entity\Session;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SessionVoter extends Voter
{
    public const OWNER = 'owner';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!\in_array($attribute, [self::OWNER])) {
            return false;
        }

        if (!$subject instanceof Session) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // the user must be logged in; if not, deny access
        if (!$user instanceof User) {
            return false;
        }

        /** @var Session $session */
        $session = $subject;

        return match ($attribute) {
            self::OWNER => $this->canView($session, $user),
            default => false,
        };
    }

    private function canView(Session $session, User $user): bool
    {
        return $session->getAuthor() === $user;
    }
}
