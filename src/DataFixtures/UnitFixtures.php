<?php

namespace App\DataFixtures;

use App\Factory\UnitFactory;
use Zenstruck\Foundry\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UnitFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            TopicFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        Factory::delayFlush(function () {
            UnitFactory::createMany(10);
        });
    }
}
