<?php

declare(strict_types=1);

namespace App\Utility;

class Random
{
    public static function getFloat(float $min, float $max): float
    {
        if ($min >= $max) {
            throw new \InvalidArgumentException(\sprintf('Min value can not be greater than the max value. Given min = %f and max = %f', $min, $max));
        }

        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
