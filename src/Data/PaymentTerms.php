<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class PaymentTerms extends AbstractData
{
    public function __construct(
        public string $Note,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'Note' => XMLNS::CBC,
            default => null
        };
    }
}
