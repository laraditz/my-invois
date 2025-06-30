<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Contracts\WithAttributes;
use Laraditz\MyInvois\Traits\HasAttributes;

class Reference extends AbstractData implements WithAttributes
{
    use HasAttributes;

    public function __construct(
        public ?array $Transforms = null,
        public ?Data $DigestMethod = null,
        public ?string $DigestValue = null,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'Transforms', 'DigestMethod', 'DigestValue' => XMLNS::DS,
            default => null
        };
    }


}
