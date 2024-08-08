<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Enum\SettingName;
use App\Enum\SettingType;

class BooleanSetting extends AbstractSetting
{
    public function __construct(SettingName $name, bool $value, int|float|string|array|null $allowedValues = null)
    {
        parent::__construct($name, $value, $allowedValues);
    }

    public function getType(): SettingType
    {
        return SettingType::BOOLEAN;
    }
}
