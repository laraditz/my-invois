<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class Contact extends AbstractData
{
    public function __construct(
        public string $Telephone,
        public string $ElectronicMail,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'Telephone', 'ElectronicMail' => XMLNS::CBC,
            default => null
        };
    }
}
