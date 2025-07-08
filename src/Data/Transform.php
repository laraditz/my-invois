<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Traits\HasAttributes;
use Laraditz\MyInvois\Contracts\WithAttributes;

class Transform extends AbstractData implements WithAttributes
{
    use HasAttributes;

    public function __construct(
        public ?string $XPath = null,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'XPath' => XMLNS::DS,
        };
    }
}
