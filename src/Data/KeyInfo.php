<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class KeyInfo extends AbstractData
{
    public function __construct(
        public X509Data $X509Data
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'X509Data' => XMLNS::DS,
            default => null
        };
    }
}
