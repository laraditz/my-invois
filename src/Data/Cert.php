<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class Cert extends AbstractData
{
    public function __construct(
        public CertDigest $CertDigest,
        public IssuerSerial $IssuerSerial
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'CertDigest', 'IssuerSerial' => XMLNS::XADES,
            default => null
        };
    }
}
