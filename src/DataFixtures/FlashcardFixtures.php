<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\FlashcardFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Zenstruck\Foundry\Factory;

class FlashcardFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            UnitFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        Factory::delayFlush(function (): void {
            FlashcardFactory::createMany(100);
        });
    }
}
