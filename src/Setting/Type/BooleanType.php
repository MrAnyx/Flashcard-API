<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Serializer\BooleanSerializer;
use Symfony\Component\Validator\Constraint;

class BooleanType extends AbstractSettingType
{
    /**
     * @param Constraint[] $constraints
     */
    public function __construct(BooleanSerializer $serializer, array $constraints)
    {
        parent::__construct($serializer, $constraints);
    }
}
