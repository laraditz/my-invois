<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class LegalMonetaryTotal extends AbstractData
{
    public function __construct(
        public Data|Money $LineExtensionAmount,
        public Data|Money $TaxExclusiveAmount,
        public Data|Money $TaxInclusiveAmount,
        public Data|Money $AllowanceTotalAmount,
        public Data|Money $ChargeTotalAmount,
        public Data|Money $PayableRoundingAmount,
        public Data|Money $PayableAmount,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'LineExtensionAmount', 'TaxExclusiveAmount', 'TaxInclusiveAmount', 'AllowanceTotalAmount',
            'ChargeTotalAmount', 'PayableRoundingAmount', 'PayableAmount' => XMLNS::CBC,
            default => null
        };
    }
}
