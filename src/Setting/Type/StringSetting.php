<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Enum\SettingName;

class StringSetting extends AbstractSetting
{
    public function __construct(SettingName $name, string $value)
    {
        parent::__construct($name, $value);
    }

    public function getType(): string
    {
        return 'string';
    }

    public function serialize(): string
    {
        return (string) $this->value;
    }
}
