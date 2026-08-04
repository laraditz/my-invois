<?php

namespace Laraditz\MyInvois\Contracts;

interface UblDocument
{
    public function getRootElement(): string;

    public function getDocumentNamespace(): string;

    public function getCodeNumber();

    public function getInvoiceTypeCode();
}
