<?php

namespace Laraditz\MyInvois\Data;

class DebitNote extends Invoice
{
    public function getRootElement(): string
    {
        return 'DebitNote';
    }

    public function getDocumentNamespace(): string
    {
        return 'urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2';
    }
}
