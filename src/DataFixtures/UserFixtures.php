<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Zenstruck\Foundry\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        Factory::delayFlush(function (): void {
            UserFactory::createOne(['username' => 'admin', 'roles' => ['ROLE_ADMIN']]);
            UserFactory::createOne(['username' => 'user']);
            UserFactory::createOne(['username' => 'test']);
        });
    }
}
