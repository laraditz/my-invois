<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class PartyIdentification extends AbstractData
{
    public function __construct(
        public Data $ID
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'ID' => XMLNS::CBC,
            default => null
        };
    }
}
