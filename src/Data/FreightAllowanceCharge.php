<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class FreightAllowanceCharge extends AbstractData
{
    public function __construct(
        public bool $ChargeIndicator,
        public string $AllowanceChargeReason,
        public Money|Data $Amount,
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
