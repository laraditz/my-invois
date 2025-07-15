<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Attributes\Attributes;
use Laraditz\MyInvois\Traits\HasAttributes;
use Laraditz\MyInvois\Contracts\WithAttributes;

#[Attributes(attrs: ['Id' => 'id-xades-signed-props'])]
class SignedProperties extends AbstractData implements WithAttributes
{
    use HasAttributes;

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
