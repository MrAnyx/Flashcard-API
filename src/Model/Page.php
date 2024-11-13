<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\Common\Collections\Order;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

readonly class Page
{
    public function __construct(
        #[NotBlank]
        #[Assert\Positive]
        public int $page,

        #[NotBlank]
        public string $sort,

        #[NotBlank]
        public Order $order,

        #[NotBlank]
        #[Assert\Positive]
        #[Assert\LessThanOrEqual(1000)]
        public int $itemsPerPage,
    ) {
    }
}
