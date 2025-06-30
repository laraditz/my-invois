<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class UBLExtension extends AbstractData
{
    public function __construct(
        public string $ExtensionURI = 'urn:oasis:names:specification:ubl:dsig:enveloped:xades',
        public ?ExtensionContent $ExtensionContent = null,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'ExtensionURI', 'ExtensionContent' => XMLNS::NONE,
            default => null
        };
    }
}
