<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Enum\SettingName;

class IntegerSetting extends AbstractSetting
{
    public function __construct(SettingName $name, int $value)
    {
        parent::__construct($name, $value);
    }

    public function getType(): string
    {
        return 'int';
    }

    public function serialize(): string
    {
        return (string) $this->value;
    }
}
