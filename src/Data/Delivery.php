<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class Delivery extends AbstractData
{
    public function __construct(
        public ?DeliveryParty $DeliveryParty = null,
        public ?Shipment $Shipment = null,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'DeliveryParty', 'Shipment' => XMLNS::CAC,
            default => null
        };
    }
}
