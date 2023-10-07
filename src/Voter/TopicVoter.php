<?php

namespace App\Voter;

use App\Entity\User;
use App\Entity\Topic;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TopicVoter extends Voter
{
    public const OWNER = 'owner';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (! in_array($attribute, [self::OWNER])) {
            return false;
        }

        if (! $subject instanceof Topic) {
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

        /** @var Topic $topic */
        $topic = $subject;

        return match ($attribute) {
            self::OWNER => $this->canView($topic, $user),
            default => false
        };
    }

    private function canView(Topic $post, User $user): bool
    {
        return $post->getAuthor() === $user;
    }
}
