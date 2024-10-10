<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Serializer\IntegerSerializer;
use Symfony\Component\Validator\Constraint;

class IntegerType extends AbstractSettingType
{
    /**
     * @param Constraint[] $constraints
     */
    public function __construct(IntegerSerializer $serializer, array $constraints)
    {
        parent::__construct($serializer, $constraints);
    }
}
