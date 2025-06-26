<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class AccountingCustomerParty extends AbstractData
{
    public function __construct(
        public Party $Party,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'Party' => XMLNS::CAC,
            default => null
        };
    }
}
