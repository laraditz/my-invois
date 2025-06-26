<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class Country extends AbstractData
{
    public function __construct(
        public Data $IdentificationCode
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
