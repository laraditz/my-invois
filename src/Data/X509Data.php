<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Attributes\Attributes;

class X509Data extends AbstractData
{
    public function __construct(
        public string $X509Certificate
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'X509Certificate' => XMLNS::DS,
            default => null
        };
    }
}
