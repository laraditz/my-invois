<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Attributes\Attributes;

#[Attributes(attrs: [
    'xmlns:' . XMLNS::SIG->value => 'urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2',
    'xmlns:' . XMLNS::SAC->value => 'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2',
    'xmlns:' . XMLNS::SBC->value => 'urn:oasis:names:specification:ubl:schema:xsd:SignatureBasicComponents-2'
])]
class UBLDocumentSignatures extends AbstractData
{
    public function __construct(
        public SignatureInformation $SignatureInformation,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'SignatureInformation' => XMLNS::SAC,
            default => null
        };
    }
}
