<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class CommodityClassification extends AbstractData
{
    public function __construct(
        public Data $ItemClassificationCode,

    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'ItemClassificationCode' => XMLNS::CBC,
            default => null
        };
    }
}
