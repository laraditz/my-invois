<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class SigningCertificate extends AbstractData
{
    public function __construct(
        public Cert $Cert
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'Cert' => XMLNS::XADES,
        };
    }
}
