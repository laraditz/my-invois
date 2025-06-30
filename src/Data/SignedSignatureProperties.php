<?php

namespace Laraditz\MyInvois\Data;

use Illuminate\Support\Carbon;
use Laraditz\MyInvois\Enums\XMLNS;

class SignedSignatureProperties extends AbstractData
{
    public function __construct(
        public Carbon|string $SigningTime,
        public SigningCertificate $SigningCertificate
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'SigningTime', 'SigningCertificate' => XMLNS::XADES,
        };
    }

    public function getValue(string $name): mixed
    {
        return match ($name) {
            'SigningTime' => $this->$name instanceof Carbon ? $this->$name?->toIso8601ZuluString() : $this->$name,
            default => $this->$name
        };
    }
}
