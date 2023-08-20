<?php

namespace App\DataFixtures;

use Zenstruck\Foundry\Factory;
use App\Factory\FlashcardFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FlashcardFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        Factory::delayFlush(function () {
            FlashcardFactory::createMany(10);
        });
    }
}
