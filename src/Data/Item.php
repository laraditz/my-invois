<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class Item extends AbstractData
{
    public function __construct(
        public string $Description,
        public OriginCountry $OriginCountry,
        public ?array $CommodityClassification = null,

    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'Description' => XMLNS::CBC,
            'OriginCountry', 'CommodityClassification' => XMLNS::CAC,
            default => null
        };
    }
}
