<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class OriginCountry extends AbstractData
{
    public function __construct(
        public string $IdentificationCode,

    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'IdentificationCode' => XMLNS::CBC,
            default => null
        };
    }
}
