<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Enum\SettingName;

class BooleanSetting extends AbstractSetting
{
    public function __construct(SettingName $name, bool $value)
    {
        parent::__construct($name, $value);
    }

    public function getType(): string
    {
        return 'bool';
    }

    public function serialize(): string
    {
        return (string) $this->value;
    }
}
