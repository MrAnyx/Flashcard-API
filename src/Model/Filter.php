<?php

declare(strict_types=1);

namespace App\Model;

use App\Enum\OperatorType;
use Symfony\Component\Validator\Constraints as Assert;

readonly class Filter
{
    public function __construct(
        #[Assert\NotBlank]
        public string $filter,

        #[Assert\NotBlank]
        public mixed $value,

        #[Assert\NotBlank]
        public OperatorType $operator,
    ) {
    }

    public function getDoctrineParameter(): mixed
    {
        return match ($this->operator) {
            OperatorType::LIKE => "%{$this->value}%",
            default => $this->value,
        };
    }
}
