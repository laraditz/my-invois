<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Contracts\WithAttributes;
use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Attributes\Attributes;
use Laraditz\MyInvois\Traits\HasAttributes;

#[Attributes(attrs: ['Target' => 'signature'])]
class QualifyingProperties extends AbstractData implements WithAttributes
{
    use HasAttributes;

    public function __construct(
        public SignedProperties $SignedProperties
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'SignedProperties' => XMLNS::XADES,
        };
    }

    public function getAttributes(): array
    {
        return [
            'xmlns:' . XMLNS::XADES() => XMLNS::XADES->getNamespace()
        ];
    }
}
