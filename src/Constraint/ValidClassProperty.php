<?php

declare(strict_types=1);

namespace App\Constraint;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidClassProperty extends Constraint
{
    public string $message = 'Property "{{ property }}" in class "{{ classname }}" doesn\' exist.';

    #[HasNamedArguments]
    public function __construct(
        public string $classname,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);
    }
}
