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

    public function test_it_signs_invoice_and_debit_note_against_their_own_root_element()
    {
        // Same field values on purpose: if the document digest were still
        // computed against a hardcoded 'Invoice' root/namespace regardless
        // of actual type, these two would produce an identical signature.
        // Different signatures prove the digest was computed against each
        // document's own getRootElement()/getDocumentNamespace().
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

        $this->assertNotSame($invoiceSignatureValue, $debitNoteSignatureValue);
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
