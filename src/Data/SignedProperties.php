<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Attributes\Attributes;

#[Attributes(attrs: ['Id' => 'id-xades-signed-props'])]
class SignedProperties extends AbstractData
{
    public function __construct(
        public SignedSignatureProperties $SignedSignatureProperties
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'SignedSignatureProperties' => XMLNS::XADES,
            default => null
        };
    }
}
