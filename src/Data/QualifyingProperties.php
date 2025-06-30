<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Attributes\Attributes;

#[Attributes(attrs: ['xmlns:' . XMLNS::XADES->value => 'http://uri.etsi.org/01903/v1.3.2#', 'Target' => 'signature'])]
class QualifyingProperties extends AbstractData
{
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
}
