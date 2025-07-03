<?php

namespace Laraditz\MyInvois;

use DOMDocument;
use Sabre\Xml\Service;
use Laraditz\MyInvois\Enums\XMLNS;

class MyInvoisHelper
{
    public function createInvoiceXMLService(): Service
    {
        $namespaces = [
            'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2' => '',
            XMLNS::CAC->getNamespace() => XMLNS::CAC(),
            XMLNS::CBC->getNamespace() => XMLNS::CBC(),
        ];

        return $this->createXMLService($namespaces);
    }

    public function createQualifyingPropertiesXMLService(): Service
    {
        $namespaces = [
            XMLNS::XADES->getNamespace() => XMLNS::XADES(),
            XMLNS::DS->getNamespace() => XMLNS::DS(),
        ];

        return $this->createXMLService($namespaces);
    }

    public function createXMLService(?array $namespaces = []): Service
    {
        $service = new Service();

        if (count($namespaces) > 0) {
            $service->namespaceMap = $namespaces;
        }

        return $service;
    }
    public function writeXml(Service $service, $rootElement = '', array $xml = []): string
    {
        $xmlData = $service->write($rootElement, $xml);

        $dom = $this->createDOM();
        $dom->loadXML($xmlData);

        return $dom->saveXML();
    }

    public function displayXml(string $xml)
    {
        header("Content-type: text/xml");
        echo $xml;
        exit;
    }

    public function createDOM(
        string $version = '1.0',
        string $encoding = 'UTF-8',
        bool $preserveWhiteSpace = false,
        bool $formatOutput = false
    ): DOMDocument {
        $dom = new DOMDocument($version, encoding: $encoding);
        $dom->preserveWhiteSpace = $preserveWhiteSpace;
        $dom->formatOutput = $formatOutput;

        return $dom;
    }

    public function removeXMLTag(string $xml)
    {
        $regex = '/<\?xml[^>]*>([\s\S]*?)*/m';

        $string = preg_replace($regex, '', $xml);
        $string = trim($string);
        // $string = str_replace(["\n", "\r"], '', $string);

        return $string;
    }



}