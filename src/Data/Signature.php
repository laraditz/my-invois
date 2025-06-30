<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Contracts\WithAttributes;
use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Attributes\Attributes;
use Laraditz\MyInvois\Traits\HasAttributes;

#[Attributes(attrs: ['Id' => 'signature'])]
class Signature extends AbstractData implements WithAttributes
{
    use HasAttributes;

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
        };
    }

    public function getAttributes(): array
    {
        return [
            'xmlns:' . XMLNS::DS() => XMLNS::DS->getNamespace(),
        ];
    }
}
