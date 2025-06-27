<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class AllowanceCharge extends AbstractData
{
    public function __construct(
        public bool $ChargeIndicator,
        public string $AllowanceChargeReason,
        public Data|Money $Amount,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'ChargeIndicator', 'AllowanceChargeReason', 'Amount' => XMLNS::CBC,
            default => null
        };
    }
}
