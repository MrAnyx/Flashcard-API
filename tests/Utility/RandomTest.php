<?php

declare(strict_types=1);

namespace App\Tests\Utility;

use App\Utility\Random;
use PHPUnit\Framework\TestCase;

class RandomTest extends TestCase
{
    public function testRandom(): void
    {
        $min = 0;
        $max = 10;

        for ($i = 0; $i < 100; ++$i) {
            $this->assertTrue(Random::getFloat($min, $max) >= $min);
            $this->assertTrue(Random::getFloat($min, $max) <= $max);
        }
    }

    public function testRandomWithInvalidParameter(): void
    {
        $min = 10;
        $max = 0;

        $this->expectException(\InvalidArgumentException::class);

        $value = Random::getFloat($min, $max);
    }
}
