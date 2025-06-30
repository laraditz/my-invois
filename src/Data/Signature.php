<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Attributes\Attributes;

#[Attributes(attrs: [
    'xmlns:' . XMLNS::DS->value => 'http://www.w3.org/2000/09/xmldsig#',
    'Id' => 'signature'
])]
class Signature extends AbstractData
{
    public function __construct(
        public SignedInfo $SignedInfo,
        public string $SignatureValue,
        public KeyInfo $KeyInfo,
        public object $Object,

    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'SignedInfo', 'SignatureValue', 'KeyInfo', 'Object' => XMLNS::DS,
            default => null
        };
    }
}
