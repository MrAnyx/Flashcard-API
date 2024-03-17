<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\TopicFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Zenstruck\Foundry\Factory;

class TopicFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        Factory::delayFlush(function (): void {
            TopicFactory::createMany(5);
        });
    }
}
