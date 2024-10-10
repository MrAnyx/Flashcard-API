<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Serializer\FloatSerializer;
use Symfony\Component\Validator\Constraint;

class FloatType extends AbstractSettingType
{
    /**
     * @param Constraint[] $constraints
     */
    public function __construct(FloatSerializer $serializer, array $constraints)
    {
        parent::__construct($serializer, $constraints);
    }
}
