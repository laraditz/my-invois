<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class TaxCategory extends AbstractData
{
    public function __construct(
        public string $ID,
        public ?string $TaxExemptionReason = null,
        public ?TaxScheme $TaxScheme = null,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'ID', 'TaxExemptionReason' => XMLNS::CBC,
            'TaxScheme' => XMLNS::CAC,
            default => null
        };
    }
}
