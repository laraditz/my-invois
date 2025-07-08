<?php

namespace Laraditz\MyInvois\Data;

use Illuminate\Support\Carbon;
use Laraditz\MyInvois\Enums\XMLNS;

class DatePeriod extends AbstractData
{
    public function __construct(
        public Carbon $StartDate,
        public Carbon $EndDate,
        public ?string $Description = 'Monthly',
    ) {
    }

    public function getValue(string $name): mixed
    {
        return match ($name) {
            'StartDate', 'EndDate' => $this->$name?->toDateString(),
            default => $this->$name
        };
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'StartDate', 'EndDate', 'Description' => XMLNS::CBC,
            default => null
        };
    }
}