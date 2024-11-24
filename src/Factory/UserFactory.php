<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use App\UniqueGenerator\UniqueTokenGenerator;
use App\Utility\Roles;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    private UserPasswordHasherInterface $passwordHasher;

    private UniqueTokenGenerator $uniqueTokenGenerator;

    public function __construct(UserPasswordHasherInterface $passwordHasher, UniqueTokenGenerator $uniqueTokenGenerator)
    {
        parent::__construct();

        $this->passwordHasher = $passwordHasher;
        $this->uniqueTokenGenerator = $uniqueTokenGenerator;
    }

    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->email(),
            'username' => self::faker()->userName(),
            'password' => 'Password1!',
            'token' => $this->uniqueTokenGenerator->generate(User::class, 'token'),
            'roles' => [Roles::User],
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function (User $user): void {
                $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            });
    }
}
