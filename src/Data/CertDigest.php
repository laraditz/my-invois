<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class CertDigest extends AbstractData
{
    public function __construct(
        public Data $DigestMethod,
        public string $DigestValue,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'DigestMethod', 'DigestValue' => XMLNS::DS,
            default => null
        };
    }
}
