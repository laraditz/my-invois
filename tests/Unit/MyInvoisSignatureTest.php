<?php

namespace Laraditz\MyInvois\Tests\Unit;

use Laraditz\MyInvois\Contracts\UblDocument;
use Laraditz\MyInvois\Data\DebitNote;
use Laraditz\MyInvois\Data\Invoice;
use Laraditz\MyInvois\Data\InvoiceTypeCode;
use Laraditz\MyInvois\MyInvoisCertificate;
use Laraditz\MyInvois\MyInvoisSignature;
use Laraditz\MyInvois\Tests\Concerns\GeneratesTestCertificate;
use Laraditz\MyInvois\Tests\TestCase;

class MyInvoisSignatureTest extends TestCase
{
    use GeneratesTestCertificate;

    public function test_it_signs_an_invoice()
    {
        $invoice = new Invoice(
            ID: 'INV-001',
            InvoiceTypeCode: new InvoiceTypeCode('01'),
        );

        $signature = new MyInvoisSignature(
            document: $invoice,
            certificate: $this->generateTestCertificate(),
        );

        $this->assertNotEmpty($signature->getUBLExtensions());
        $this->assertNotEmpty($signature->getSignature());
    }

    public function test_it_signs_a_debit_note()
    {
        $debitNote = new DebitNote(
            ID: 'DN-001',
            InvoiceTypeCode: new InvoiceTypeCode('03'),
        );

        $signature = new MyInvoisSignature(
            document: $debitNote,
            certificate: $this->generateTestCertificate(),
        );

        $this->assertNotEmpty($signature->getUBLExtensions());
        $this->assertNotEmpty($signature->getSignature());
    }

    public function test_it_signs_a_debit_note_against_the_invoice_root_element()
    {
        // MyInvois validates every document type against the UBL Invoice
        // schema, so a debit note must be digested as <Invoice> (Invoice-2).
        // With identical field values, a debit note must therefore produce
        // the same signature as an invoice.
        $certificate = $this->generateTestCertificate();

        $invoice = new Invoice(
            ID: 'DOC-001',
            InvoiceTypeCode: new InvoiceTypeCode('01'),
        );

        $debitNote = new DebitNote(
            ID: 'DOC-001',
            InvoiceTypeCode: new InvoiceTypeCode('01'),
        );

        $invoiceSignatureValue = $this->signatureValue($invoice, $certificate);
        $debitNoteSignatureValue = $this->signatureValue($debitNote, $certificate);

        $this->assertSame($invoiceSignatureValue, $debitNoteSignatureValue);
    }

    private function signatureValue(UblDocument $document, MyInvoisCertificate $certificate): string
    {
        $signature = new MyInvoisSignature(document: $document, certificate: $certificate);

        return $signature->getUBLExtensions()
            ->UBLExtension
            ->ExtensionContent
            ->UBLDocumentSignatures
            ->SignatureInformation
            ->Signature
            ->SignatureValue;
    }
}
