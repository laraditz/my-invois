<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class AddressLine extends AbstractData
{
    public function __construct(
        public string $Line
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'Line' => XMLNS::CBC,
            default => null
        };
    }
}