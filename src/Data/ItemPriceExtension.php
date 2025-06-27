<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class ItemPriceExtension extends AbstractData
{
    public function __construct(
        public Data|Money $Amount,

    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'Amount' => XMLNS::CBC,
            default => null
        };
    }
}
