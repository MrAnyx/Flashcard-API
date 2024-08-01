<?php

declare(strict_types=1);

namespace App\Setting\Type;

use App\Enum\SettingName;
use App\Enum\SettingType;

abstract class AbstractSetting
{
    public function __construct(
        public readonly SettingName $name,
        public readonly mixed $value
    ) {
    }

    abstract public function getType(): SettingType;

    abstract public function serialize(): string;
}
