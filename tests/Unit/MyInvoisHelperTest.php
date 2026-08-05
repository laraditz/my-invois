<?php

namespace Laraditz\MyInvois\Tests\Unit;

use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\MyInvoisHelper;
use Laraditz\MyInvois\Tests\TestCase;

class MyInvoisHelperTest extends TestCase
{
    public function test_create_document_xml_service_maps_the_given_namespace()
    {
        $helper = new MyInvoisHelper();

        $service = $helper->createDocumentXMLService('urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2');

        $this->assertSame('', $service->namespaceMap['urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2']);
        $this->assertSame(XMLNS::CAC(), $service->namespaceMap[XMLNS::CAC->getNamespace()]);
        $this->assertSame(XMLNS::CBC(), $service->namespaceMap[XMLNS::CBC->getNamespace()]);
    }

    public function test_create_invoice_xml_service_still_maps_invoice_namespace()
    {
        $helper = new MyInvoisHelper();

        $service = $helper->createInvoiceXMLService();

        $this->assertSame('', $service->namespaceMap['urn:oasis:names:specification:ubl:schema:xsd:Invoice-2']);
    }
}
