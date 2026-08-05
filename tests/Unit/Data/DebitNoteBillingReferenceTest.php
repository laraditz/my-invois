<?php

namespace Laraditz\MyInvois\Tests\Unit\Data;

use Laraditz\MyInvois\Data\BillingReference;
use Laraditz\MyInvois\Data\DebitNote;
use Laraditz\MyInvois\Data\InvoiceDocumentReference;
use Laraditz\MyInvois\Tests\TestCase;

class DebitNoteBillingReferenceTest extends TestCase
{
    public function test_billing_reference_round_trips_through_to_xml_array()
    {
        $debitNote = new DebitNote(
            ID: 'DN-001',
            BillingReference: [
                new BillingReference(
                    InvoiceDocumentReference: [
                        new InvoiceDocumentReference(ID: 'INV-001', UUID: 'ABC123'),
                    ],
                ),
            ],
        );

        $xmlArray = $debitNote->toXmlArray();

        $billingReferenceNode = collect($xmlArray)->firstWhere('name', 'cac:BillingReference');
        $this->assertNotNull($billingReferenceNode);

        $invoiceDocumentReferenceNode = collect($billingReferenceNode['value'])
            ->firstWhere('name', 'cac:InvoiceDocumentReference');
        $this->assertNotNull($invoiceDocumentReferenceNode);

        $this->assertSame('INV-001', $invoiceDocumentReferenceNode['value']['cbc:ID']);
        $this->assertSame('ABC123', $invoiceDocumentReferenceNode['value']['cbc:UUID']);
    }
}
