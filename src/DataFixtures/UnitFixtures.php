<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\UnitFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Zenstruck\Foundry\Factory;

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
        Factory::delayFlush(function (): void {
            UnitFactory::createMany(10);
        });
    }
}
