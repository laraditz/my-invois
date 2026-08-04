<?php

namespace Laraditz\MyInvois\Tests\Feature;

use Laraditz\MyInvois\Data\Invoice;
use Laraditz\MyInvois\Data\InvoiceTypeCode;
use Laraditz\MyInvois\MyInvois;
use Laraditz\MyInvois\Tests\TestCase;

class GenerateDocumentTest extends TestCase
{
    public function test_it_generates_invoice_xml_with_invoice_root_element()
    {
        $invoice = new Invoice(
            ID: 'INV-001',
            InvoiceTypeCode: new InvoiceTypeCode('01'),
        );

        $myInvois = new MyInvois(client_id: 'test-client', client_secret: 'test-secret');

        $xml = $myInvois->generateXMLDocument($invoice);

        $this->assertStringContainsString('<Invoice', $xml);
        $this->assertStringContainsString(
            'xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"',
            $xml
        );
    }
}
