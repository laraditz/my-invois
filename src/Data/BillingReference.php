<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class BillingReference extends AbstractData
{
    public function __construct(
        public ?array $InvoiceDocumentReference = [],
        public ?array $AdditionalDocumentReference = [],
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'InvoiceDocumentReference', 'AdditionalDocumentReference' => XMLNS::CAC,
            default => null
        };
    }
}