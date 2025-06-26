<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class Party extends AbstractData
{
    public function __construct(
        public ?Data $IndustryClassificationCode = null,
        public ?array $PartyIdentification = [],
        public ?PostalAddress $PostalAddress = null,
        public ?PartyLegalEntity $PartyLegalEntity = null,
        public ?Contact $Contact = null,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'IndustryClassificationCode' => XMLNS::CBC,
            'PartyIdentification', 'PostalAddress', 'PartyLegalEntity', 'Contact' => XMLNS::CAC,
            default => null
        };
    }
}
