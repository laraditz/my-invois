<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class AccountingSupplierParty extends AbstractData
{
    public function __construct(
        public ?Data $AdditionalAccountID = null,
        public ?Party $Party = null,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'AdditionalAccountID' => XMLNS::CBC,
            'Party' => XMLNS::CAC,
            default => null
        };
    }
}
