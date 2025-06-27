<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class InvoiceLine extends AbstractData
{
    public function __construct(
        public string $ID,
        public Data $InvoicedQuantity,
        public Data|Money $LineExtensionAmount,
        public ?array $AllowanceCharge = [],
        public ?TaxTotal $TaxTotal = null,
        public ?Item $Item = null,
        public ?Price $Price = null,
        public ?ItemPriceExtension $ItemPriceExtension = null,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'ID', 'InvoicedQuantity', 'LineExtensionAmount' => XMLNS::CBC,
            'AllowanceCharge', 'TaxTotal', 'Item', 'Price', 'ItemPriceExtension' => XMLNS::CAC,
            default => null
        };
    }
}
