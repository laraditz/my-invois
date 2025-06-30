<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class SignatureInformation extends AbstractData
{
    public function __construct(
        public ?string $ID = 'urn:oasis:names:specification:ubl:signature:1',
        public ?string $ReferencedSignatureID = 'urn:oasis:names:specification:ubl:signature:Invoice',
        public ?Signature $Signature,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'ID' => XMLNS::CBC,
            'ReferencedSignatureID' => XMLNS::SBC,
            'Signature' => XMLNS::DS,
            default => null
        };
    }
}
