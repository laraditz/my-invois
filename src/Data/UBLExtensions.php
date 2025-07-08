<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Attributes\Attributes;

#[Attributes(attrs: ['xmlns' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2'])]
class UBLExtensions extends AbstractData
{
    public function __construct(
        public UBLExtension $UBLExtension,

    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'UBLExtension' => XMLNS::NONE,
            default => null
        };
    }
}
