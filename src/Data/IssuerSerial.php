<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Attributes\Attributes;

class IssuerSerial extends AbstractData
{
    public function __construct(
        public Data|string $X509IssuerName,
        public Data|string $X509SerialNumber
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'X509IssuerName', 'X509SerialNumber' => XMLNS::DS,
        };
    }
}
