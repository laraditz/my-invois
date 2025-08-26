<?php

namespace Laraditz\MyInvois\Data;

use Illuminate\Support\Carbon;
use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Enums\Frequency;

class DatePeriod extends AbstractData
{
    public function __construct(
        public Carbon|string $StartDate,
        public Carbon|string $EndDate,
        public Frequency|string $Description = Frequency::Monthly,
    ) {
    }

    public function getValue(string $name): mixed
    {
        return match ($name) {
            'StartDate', 'EndDate' => $this->$name instanceof Carbon ? $this->$name?->toDateString() : $this->$name,
            'Description' => $this->$name instanceof Frequency ? $this->Description?->value : $this->$name,
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