<?php

namespace App\Voter;

use App\Entity\User;
use App\Entity\Flashcard;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class FlashcardVoter extends Voter
{
    public const OWNER = 'owner';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::OWNER])) {
            return false;
        }

        if (!$subject instanceof Flashcard) {
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

        /** @var Flashcard $flashcard */
        $flashcard = $subject;

        return match ($attribute) {
            self::OWNER => $this->canView($flashcard, $user),
            default => false
        };
    }

    private function canView(Flashcard $flashcard, User $user): bool
    {
        return $flashcard->getUnit()->getTopic()->getAuthor() === $user;
    }
}
