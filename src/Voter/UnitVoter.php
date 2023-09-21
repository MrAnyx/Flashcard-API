<?php

namespace App\Voter;

use App\Entity\Unit;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UnitVoter extends Voter
{
    public const OWNER = 'owner';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (! in_array($attribute, [self::OWNER])) {
            return false;
        }

        if (! $subject instanceof Unit) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // the user must be logged in; if not, deny access
        if (! $user instanceof User) {
            return false;
        }

        /** @var Unit $unit */
        $unit = $subject;

        return match ($attribute) {
            self::OWNER => $this->canView($unit, $user),
        };
    }

    private function canView(Unit $unit, User $user): bool
    {
        return $unit->getTopic()->getAuthor() === $user;
    }
}
