<?php

declare(strict_types=1);

namespace App\SpacedRepetition;

class Fsrs4_5Algorithm extends Fsrs4Algorithm
{
    protected array $w = [0.4872, 1.4003, 3.7145, 13.8206, 5.1618, 1.2298, 0.8975, 0.031, 1.6474, 0.1367, 1.0461, 2.1072, 0.0793, 0.3246, 1.587, 0.2272, 2.8755];

    protected float $decay = -0.5;

    protected float $factor = 19 / 81;
}
