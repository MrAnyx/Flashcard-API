<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Serializer\StringSerializer;
use Symfony\Component\Validator\Constraint;

class StringType extends AbstractSettingType
{
    /**
     * @param Constraint[] $constraints
     */
    public function __construct(StringSerializer $serializer, array $constraints)
    {
        parent::__construct($serializer, $constraints);
    }
}
