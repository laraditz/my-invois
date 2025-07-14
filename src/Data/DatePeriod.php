<?php

namespace Laraditz\MyInvois\Data;

use Illuminate\Support\Carbon;
use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Enums\Frequency;

class DatePeriod extends AbstractData
{
    public function __construct(
        public Carbon $StartDate,
        public Carbon $EndDate,
        public ?Frequency $Description = Frequency::Monthly,
    ) {
    }

    public function getValue(string $name): mixed
    {
        return match ($name) {
            'StartDate', 'EndDate' => $this->$name?->toDateString(),
            'Description' => $this->Description?->value,
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