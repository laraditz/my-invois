<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class PayeeFinancialAccount extends AbstractData
{
    public function __construct(
        public string $ID,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'ID' => XMLNS::CBC,
            default => null
        };
    }
}
