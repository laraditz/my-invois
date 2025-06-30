<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Contracts\WithAttributes;
use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Attributes\Attributes;
use Laraditz\MyInvois\Traits\HasAttributes;

class Signature extends AbstractData implements WithAttributes
{
    use HasAttributes;

    public function __construct(
        public ?string $ID = null,
        public ?string $SignatureMethod = null,
        public ?SignedInfo $SignedInfo = null,
        public ?string $SignatureValue = null,
        public ?KeyInfo $KeyInfo = null,
        public ?DataObject $Object = null,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'SignedInfo', 'SignatureValue', 'KeyInfo', 'Object' => XMLNS::DS,
            'ID', 'SignatureMethod' => XMLNS::CBC
        };
    }
}
