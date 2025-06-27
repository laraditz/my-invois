<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class Shipment extends AbstractData
{
    public function __construct(
        public string $ID,
        public ?FreightAllowanceCharge $FreightAllowanceCharge = null
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'ID' => XMLNS::CBC,
            'FreightAllowanceCharge' => XMLNS::CAC,
            default => null
        };
    }
}
