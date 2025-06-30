<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class DataObject extends AbstractData
{
    public function __construct(
        public QualifyingProperties $QualifyingProperties
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'QualifyingProperties' => XMLNS::XADES,
        };
    }
}
