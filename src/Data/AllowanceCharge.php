<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class AllowanceCharge extends AbstractData
{
    public function __construct(
        public bool $ChargeIndicator,
        public string $AllowanceChargeReason,
        public ?string $MultiplierFactorNumeric = null,
        public Data|Money|null $Amount = null,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'ChargeIndicator', 'AllowanceChargeReason', 'MultiplierFactorNumeric', 'Amount' => XMLNS::CBC,
            default => null
        };
    }
}
