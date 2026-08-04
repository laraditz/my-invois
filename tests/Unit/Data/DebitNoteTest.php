<?php

namespace Laraditz\MyInvois\Tests\Unit\Data;

use Laraditz\MyInvois\Data\DebitNote;
use Laraditz\MyInvois\Data\InvoiceTypeCode;
use Laraditz\MyInvois\Tests\TestCase;

class DebitNoteTest extends TestCase
{
    public function test_it_reports_its_root_element()
    {
        $debitNote = new DebitNote();

        $this->assertSame('DebitNote', $debitNote->getRootElement());
    }

    public function test_it_reports_its_document_namespace()
    {
        $debitNote = new DebitNote();

        $this->assertSame(
            'urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2',
            $debitNote->getDocumentNamespace()
        );
    }

    public function test_it_reports_code_number_and_invoice_type_code()
    {
        $debitNote = new DebitNote(
            ID: 'DN-001',
            InvoiceTypeCode: new InvoiceTypeCode('03'),
        );

        $this->assertSame('DN-001', $debitNote->getCodeNumber());
        $this->assertSame('03', $debitNote->getInvoiceTypeCode());
    }
}
