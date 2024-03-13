<?php

declare(strict_types=1);

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use Nelmio\CorsBundle\NelmioCorsBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MakerBundle\MakerBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Zenstruck\Foundry\ZenstruckFoundryBundle;

return [
    FrameworkBundle::class => ['all' => true],
    DoctrineBundle::class => ['all' => true],
    DoctrineMigrationsBundle::class => ['all' => true],
    MakerBundle::class => ['dev' => true],
    DoctrineFixturesBundle::class => ['dev' => true, 'test' => true],
    ZenstruckFoundryBundle::class => ['dev' => true, 'test' => true],
    SecurityBundle::class => ['all' => true],
    MonologBundle::class => ['all' => true],
    NelmioCorsBundle::class => ['all' => true],
];
