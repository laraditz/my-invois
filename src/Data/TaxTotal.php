<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class TaxTotal extends AbstractData
{
    public function __construct(
        public Data|Money $TaxAmount,
        public TaxSubtotal $TaxSubtotal,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'TaxSubtotal' => XMLNS::CAC,
            'TaxAmount' => XMLNS::CBC,
            default => null
        };
    }
}
