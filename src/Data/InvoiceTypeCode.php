<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Traits\HasAttributes;
use Laraditz\MyInvois\Contracts\WithAttributes;

class InvoiceTypeCode implements WithAttributes
{
    use HasAttributes;

    public function __construct(
        public string $value,
        public string $listVersionID = '1.1', //with signature
    ) {
        $this->setAttributes([
            'listVersionID' => $listVersionID,
        ]);
    }
}
