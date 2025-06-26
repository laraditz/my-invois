<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class InvoiceDocumentReference extends AbstractData
{
    public function __construct(
        public string $ID,
        public string $UUID,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'ID', 'UUID' => XMLNS::CBC,
            default => null
        };
    }
}