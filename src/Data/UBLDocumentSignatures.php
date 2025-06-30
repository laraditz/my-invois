<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Contracts\WithAttributes;
use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Traits\HasAttributes;

class UBLDocumentSignatures extends AbstractData implements WithAttributes
{
    use HasAttributes;

    public function __construct(
        public SignatureInformation $SignatureInformation,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'SignatureInformation' => XMLNS::SAC,
        };
    }

    public function getAttributes(): array
    {
        return [
            'xmlns:' . XMLNS::SIG() => XMLNS::SIG->getNamespace(),
            'xmlns:' . XMLNS::SAC() => XMLNS::SAC->getNamespace(),
            'xmlns:' . XMLNS::SBC() => XMLNS::SBC->getNamespace(),
        ];
    }
}
