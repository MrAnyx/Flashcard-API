<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Enum\SettingName;

class FloatSetting extends AbstractSetting
{
    public function __construct(SettingName $name, float $value)
    {
        parent::__construct($name, $value);
    }

    public function getType(): string
    {
        return 'float';
    }

    public function serialize(): string
    {
        return (string) $this->value;
    }
}
