<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Attributes\Attributes;

class SignedInfo extends AbstractData
{
    public function __construct(
        public Data $CanonicalizationMethod,
        public Data $SignatureMethod,
        public ?array $Reference = null,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'CanonicalizationMethod', 'SignatureMethod', 'Reference' => XMLNS::DS,
        };
    }
}
