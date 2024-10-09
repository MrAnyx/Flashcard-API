<?php

declare(strict_types=1);

namespace App\Tests\UniqueGenerator;

use App\UniqueGenerator\AbstractUniqueGenerator;
use App\UniqueGenerator\UniqueTokenGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UniqueTokenGeneratorTest extends KernelTestCase
{
    private UniqueTokenGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = self::getContainer()->get(UniqueTokenGenerator::class);
    }

    public function testInheritance(): void
    {
        $this->assertInstanceOf(AbstractUniqueGenerator::class, $this->generator);
    }
}
