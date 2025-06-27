<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class Price extends AbstractData
{
    public function __construct(
        public Data|Money $PriceAmount,

    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'PriceAmount' => XMLNS::CBC,
            default => null
        };
    }
}
