<?php

namespace Laraditz\MyInvois\Tests\Feature;

use Laraditz\MyInvois\Data\DebitNote;
use Laraditz\MyInvois\Data\Invoice;
use Laraditz\MyInvois\Data\InvoiceTypeCode;
use Laraditz\MyInvois\Data\SelfBilledDebitNote;
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

    public function test_it_generates_debit_note_xml_with_debit_note_root_element()
    {
        $debitNote = new DebitNote(
            ID: 'DN-001',
            InvoiceTypeCode: new InvoiceTypeCode('03'),
        );

        $myInvois = new MyInvois(client_id: 'test-client', client_secret: 'test-secret');

        $xml = $myInvois->generateXMLDocument($debitNote);

        $this->assertStringContainsString('<DebitNote', $xml);
        $this->assertStringContainsString(
            'xmlns="urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2"',
            $xml
        );
    }

    public function test_it_generates_self_billed_debit_note_xml_with_debit_note_root_element()
    {
        $selfBilledDebitNote = new SelfBilledDebitNote(
            ID: 'DN-002',
            InvoiceTypeCode: new InvoiceTypeCode('13'),
        );

        $myInvois = new MyInvois(client_id: 'test-client', client_secret: 'test-secret');

        $xml = $myInvois->generateXMLDocument($selfBilledDebitNote);

        $this->assertStringContainsString('<DebitNote', $xml);
        $this->assertStringContainsString(
            'xmlns="urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2"',
            $xml
        );
    }
}
