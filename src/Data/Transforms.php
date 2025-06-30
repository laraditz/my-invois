<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class Transforms extends AbstractData
{
    public function __construct(
        public array $Transform,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'Transform' => XMLNS::DS,
        };
    }
}
