<?php

namespace Laraditz\MyInvois\Tests\Unit\Data;

use Laraditz\MyInvois\Contracts\UblDocument;
use Laraditz\MyInvois\Data\Data;
use Laraditz\MyInvois\Data\Invoice;
use Laraditz\MyInvois\Data\InvoiceTypeCode;
use Laraditz\MyInvois\Tests\TestCase;

class InvoiceTest extends TestCase
{
    public function test_it_implements_ubl_document()
    {
        $invoice = new Invoice(
            ID: 'INV-001',
            InvoiceTypeCode: new InvoiceTypeCode('01'),
        );

        $this->assertInstanceOf(UblDocument::class, $invoice);
    }

    public function test_it_reports_its_root_element()
    {
        $invoice = new Invoice();

        $this->assertSame('Invoice', $invoice->getRootElement());
    }

    public function test_it_reports_its_document_namespace()
    {
        $invoice = new Invoice();

        $this->assertSame(
            'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
            $invoice->getDocumentNamespace()
        );
    }

    public function test_it_reports_code_number_and_invoice_type_code()
    {
        $invoice = new Invoice(
            ID: 'INV-001',
            InvoiceTypeCode: new InvoiceTypeCode('01'),
        );

        $this->assertSame('INV-001', $invoice->getCodeNumber());
        $this->assertSame('01', $invoice->getInvoiceTypeCode());
    }
}
