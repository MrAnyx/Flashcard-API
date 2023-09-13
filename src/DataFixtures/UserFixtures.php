<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        Factory::delayFlush(function () {
            UserFactory::createOne(['username' => 'admin', 'roles' => ['ROLE_ADMIN']]);
            UserFactory::createOne(['username' => 'user']);
            UserFactory::createOne(['username' => 'test']);
        });
    }
}
