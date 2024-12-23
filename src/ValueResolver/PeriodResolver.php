<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Model\Period;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class PeriodResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== Period::class) {
            return [];
        }

        if (!$request->query->has('from') || !$request->query->has('to')) {
            return [null];
        }

        $from = $request->query->getString('from');
        $to = $request->query->getString('to');

        $fromDateTime = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::ATOM, $from);
        $toDateTime = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::ATOM, $to);

        if (!$fromDateTime || !$toDateTime) {
            return [null];
        }

        $period = new Period($fromDateTime, $toDateTime);

        return [$period];
    }
}
