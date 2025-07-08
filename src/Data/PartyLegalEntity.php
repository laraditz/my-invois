<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class PartyLegalEntity extends AbstractData
{
    public function __construct(
        public Data $RegistrationName
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'RegistrationName' => XMLNS::CBC,
            default => null
        };
    }
}
