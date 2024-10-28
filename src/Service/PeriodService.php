<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\PeriodType;
use App\Model\Period;

class PeriodService
{
    public function getDateTimePeriod(PeriodType $periodType): Period
    {
        $now = new \DateTimeImmutable();

        return match ($periodType) {
            PeriodType::ALL => new Period(new \DateTimeImmutable('1970-01-01 00:00:00'), $now),
            PeriodType::TODAY => new Period($now->setTime(0, 0, 0, 0), $now),
            PeriodType::YESTERDAY => new Period($now->modify('-1 day')->setTime(0, 0, 0, 0), $now->modify('-1 day')->setTime(23, 59, 59, 999)),
            PeriodType::LAST_7_DAYS => new Period($now->modify('-7 days')->setTime(0, 0, 0, 0), $now),
            PeriodType::LAST_14_DAYS => new Period($now->modify('-14 days')->setTime(0, 0, 0, 0), $now),
            PeriodType::LAST_30_DAYS => new Period($now->modify('-30 days')->setTime(0, 0, 0, 0), $now),
            PeriodType::LAST_90_DAYS => new Period($now->modify('-90 days')->setTime(0, 0, 0, 0), $now),
        };
    }
}
