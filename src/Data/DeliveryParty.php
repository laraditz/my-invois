<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class DeliveryParty extends AbstractData
{
    public function __construct(
        public array $PartyIdentification,
        public ?PostalAddress $PostalAddress = null,
        public ?PartyLegalEntity $PartyLegalEntity = null,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'PartyIdentification', 'PostalAddress', 'PartyLegalEntity' => XMLNS::CAC,
            default => null
        };
    }
}
