<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\UserFactory;
use App\Utility\Roles;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

use function Zenstruck\Foundry\Persistence\flush_after;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['all', 'only_user'];
    }

    public function load(ObjectManager $manager): void
    {
        flush_after(function (): void {
            UserFactory::createOne(['username' => 'admin', 'roles' => [Roles::Admin]]);
            UserFactory::createOne(['username' => 'user']);
            UserFactory::createOne(['username' => 'premium', 'roles' => [Roles::Premium], 'premiumAt' => new \DateTimeImmutable()]);
        });
    }
}
