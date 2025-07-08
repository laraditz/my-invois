<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class ExtensionContent extends AbstractData
{
    public function __construct(
        public UBLDocumentSignatures $UBLDocumentSignatures,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'UBLDocumentSignatures' => XMLNS::SIG,
            default => null
        };
    }
}
