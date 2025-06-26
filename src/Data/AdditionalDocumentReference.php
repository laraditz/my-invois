<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class AdditionalDocumentReference extends AbstractData
{
    public function __construct(
        public string $ID,
        public ?string $DocumentType = null,
        public ?string $DocumentDescription = null,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'ID', 'DocumentType', 'DocumentDescription' => XMLNS::CBC,
            default => null
        };
    }
}
