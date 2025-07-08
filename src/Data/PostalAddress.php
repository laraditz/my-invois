<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class PostalAddress extends AbstractData
{
    public function __construct(
        public string $CityName,
        public string $PostalZone,
        public string $CountrySubentityCode,
        public array $AddressLine,
        public Country $Country,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'CityName', 'PostalZone', 'CountrySubentityCode' => XMLNS::CBC,
            'AddressLine', 'Country' => XMLNS::CAC,
            default => null
        };
    }
}
