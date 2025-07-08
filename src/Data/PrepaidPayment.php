<?php

namespace Laraditz\MyInvois\Data;

use Illuminate\Support\Carbon;
use Laraditz\MyInvois\Enums\XMLNS;

class PrepaidPayment extends AbstractData
{
    public function __construct(
        public string $ID,
        public Data $PaidAmount,
        public Carbon|string $PaidDate,
        public Carbon|string $PaidTime,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'ID', 'PaidAmount', 'PaidDate', 'PaidTime' => XMLNS::CBC,
            default => null
        };
    }

    public function getValue(string $name): mixed
    {
        return match ($name) {
            'PaidDate' => $this->$name instanceof Carbon ? $this->$name?->toDateString() : $this->$name,
            'PaidTime' => $this->$name instanceof Carbon ? $this->$name?->format('H:i:s\Z') : $this->$name,
            default => $this->$name
        };
    }
}
