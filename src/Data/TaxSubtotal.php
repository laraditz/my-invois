<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class TaxSubtotal extends AbstractData
{
    public function __construct(
        public Data|Money $TaxableAmount,
        public Data|Money $TaxAmount,
        public ?string $Percent = null,
        public ?TaxCategory $TaxCategory = null,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'TaxableAmount', 'TaxAmount', 'Percent' => XMLNS::CBC,
            'TaxCategory' => XMLNS::CAC,
            default => null
        };
    }
}
