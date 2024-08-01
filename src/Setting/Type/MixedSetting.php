<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Enum\SettingName;
use App\Enum\SettingType;

class MixedSetting extends AbstractSetting
{
    public function __construct(SettingName $name, mixed $value)
    {
        parent::__construct($name, $value);
    }

    public function getType(): SettingType
    {
        return SettingType::MIXED;
    }

    public function serialize(): string
    {
        return (string) $this->value;
    }
}
