<?php

namespace Laraditz\MyInvois;

use DOMDocument;
use LogicException;
use Sabre\Xml\Service;
use BadMethodCallException;
use Illuminate\Support\Str;
use Laraditz\MyInvois\Data\Cert;
use Laraditz\MyInvois\Data\Data;
use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Data\KeyInfo;
use Laraditz\MyInvois\Enums\Format;
use Laraditz\MyInvois\Data\X509Data;
use Laraditz\MyInvois\Data\Reference;
use Laraditz\MyInvois\Data\Signature;
use Laraditz\MyInvois\Data\CertDigest;
use Laraditz\MyInvois\Data\DataObject;
use Laraditz\MyInvois\Data\SignedInfo;
use Laraditz\MyInvois\Data\IssuerSerial;
use Laraditz\MyInvois\Data\UBLExtension;
use Laraditz\MyInvois\Data\UBLExtensions;
use Laraditz\MyInvois\Data\ExtensionContent;
use Laraditz\MyInvois\Data\SignedProperties;
use Laraditz\MyInvois\Data\SigningCertificate;
use Laraditz\MyInvois\Data\QualifyingProperties;
use Laraditz\MyInvois\Data\SignatureInformation;
use Laraditz\MyInvois\Data\UBLDocumentSignatures;
use Laraditz\MyInvois\Models\MyinvoisAccessToken;
use Laraditz\MyInvois\Exceptions\MyInvoisApiError;
use Laraditz\MyInvois\Data\SignedSignatureProperties;

class MyInvois
{
    private $services = ['auth', 'document_type', 'taxpayer', 'notification', 'document'];

    public function __construct(
        private string $client_id,
        private string $client_secret,
    ) {
    }

    public function __call($method, $arguments)
    {
        throw_if(!$this->getClientId(), LogicException::class, __('Missing Client ID.'));
        throw_if(!$this->getClientSecret(), LogicException::class, __('Missing Client Secret.'));

        if (count($arguments) > 0) {
            $argumentCollection = collect($arguments);

            try {
                $argumentCollection->keys()->ensure('string');
            } catch (\Throwable $th) {
                // throw $th;
                throw new LogicException(__('Please pass a named arguments in :method method.', ['method' => $method]));
            }
        }

        $property_name = Str::of($method)->snake()->lower()->value;

        if (in_array($property_name, $this->services)) {
            $reformat_property_name = ucfirst(Str::camel($method));

            $service_name = 'Laraditz\\MyInvois\\Services\\' . $reformat_property_name . 'Service';

            return new $service_name(app('myinvois'));
        } else {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                get_class(),
                $method
            ));
        }
    }

    public function getClientId(): string
    {
        return $this->client_id;
    }

    public function getClientSecret(): string
    {
        return $this->client_secret;
    }

    public function isSandbox(): bool
    {
        return $this->config('sandbox.mode');
    }

    public function config(string $name): array|string|int|bool
    {
        return config('myinvois.' . $name);
    }

    public function getAccessToken(): ?string
    {
        $accessTokenModel = MyinvoisAccessToken::query()
            ->where('client_id', $this->getClientId())
            ->hasNotExpired()
            ->first();



        if ($accessTokenModel) {
            return $accessTokenModel->access_token;
        } else {
            $myinvois = MyInvois::auth()->token(
                client_id: $this->getClientId(),
                client_secret: $this->getClientSecret(),
                grant_type: 'client_credentials',
                scope: 'InvoicingAPI'
            );

            $accessToken = data_get($myinvois, 'access_token');

            if (!$accessToken) {
                throw new MyInvoisApiError($result ?? ['code' => __('Missing an access token')]);
            }

            return $accessToken;
        }
    }

    public function generateDocument(mixed $data, Format $format, bool $hasSignature = true)
    {
        return match ($format) {
            Format::XML => $this->generateXMLDocument($data, $hasSignature),
            Format::JSON => $this->generateJSONDocument($data),
        };
    }

    public function generateXMLDocument($data, bool $hasSignature = true)
    {
        $service = new Service();
        $ns = 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2';

        $service->namespaceMap = [
            $ns => '',
            XMLNS::CAC->getNamespace() => XMLNS::CAC(),
            XMLNS::CBC->getNamespace() => XMLNS::CBC(),
        ];

        $signature = $this->loadSignature($data);
        // dd($signature);

        // add signature to document
        $data->add('UBLExtensions', $signature);


        $documentXml = $service->write('Invoice', $data->toXmlArray());
        // dd('test');

        $dom = $this->createDOM();
        $dom->loadXML($documentXml);

        header("Content-type: text/xml");
        echo $dom->saveXML();
        exit;




        // $document = preg_replace('/<\?xml[^>]*>([\s\S]*?)/m', '', $dom->saveXML());

        // header("Content-type: text/xml");
        // echo $document;
        // exit;

        $parentPath = "//ns:Invoice";

        $xpath = new \DomXPath($dom);
        $xpath->registerNameSpace('ns', $ns);
        // $xpath->registerNameSpace(XMLNS::CBC(), XMLNS::CBC->getNamespace());
        $parent = $xpath->query($parentPath);

        $firstSibling = $parent->item(0)->firstChild;
        // dd($firstSibling);
        // $firstSiblingDom = $this->createDOM();
        // $firstSiblinNode = $firstSiblingDom->importNode($firstSibling, true);
        // $firstSiblingDom->appendChild($firstSiblinNode);

        // header(header: "Content-type: text/xml");
        // echo $firstSiblingDom->saveXML();
        // exit;
        $newNode = $dom->importNode($signature->documentElement, true);
        $parent->item(0)->insertBefore($newNode, $firstSibling);


        // $newNode = $dom->importNode($signature->documentElement, TRUE);

        // $parent->item(0)->insertBefore($newNode, $next->item(0));

        // remove unused tags  
        $content = $dom->saveXML();
        // $content = $this->cleanUp($dom);


        header("Content-type: text/xml");
        echo $content;
        exit;


        // dd($data);

        // $document = $data->toXmlArray();



        // return $service->write('Invoice', $data->toXmlArray());
    }


    private function loadSignature(mixed $data)
    {

        $cert = new Cert(
            CertDigest: new CertDigest(
                DigestMethod: new Data('', ['Algorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256']),
                DigestValue: 'KKBSTyiPKGkGl1AFqcPziKCEIDYGtnYUTQN4ukO7G40=',
            ),
            IssuerSerial: new IssuerSerial(
                X509IssuerName: 'CN=Trial LHDNM Sub CA V1, OU=Terms of use at http://www.posdigicert.com.my, O=LHDNM, C=MY',
                X509SerialNumber: '162880276254639189035871514749820882117'
            )
        );

        $object = new DataObject(
            QualifyingProperties: new QualifyingProperties(
                new SignedProperties(
                    new SignedSignatureProperties(
                        SigningTime: now(),
                        SigningCertificate: new SigningCertificate(
                            Cert: $cert
                        ),
                    )
                )
            )
        );

        // $references[] = [
        //     'name' => 'Reference',
        //     'value' => [
        //         'name' => 'Transforms',
        //         'value' => [
        //             [
        //                 'name' => 'Transform'
        //                 'value' => 
        //             ]
        //         ]
        //     ],
        // ];

        $signInfo = new SignedInfo(
            CanonicalizationMethod: new Data('', ['Algorithm' => 'http://www.w3.org/2001/10/xml-exc-c14n#']),
            SignatureMethod: new Data('', ['Algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256']),
            Reference: [
                (new Reference(
                    Transforms: [],
                    DigestMethod: new Data('', ['Algorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256']),
                    DigestValue: 'fRaWJINS9sB9aSl/MhCjMsdVMFpLwnxstpPhJkJwkU4=',
                ))->add('attributes', ['Id' => 'id-doc-signed-data', 'URI' => '']),
                new Reference(
                    DigestMethod: new Data('', ['Algorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256']),
                    DigestValue: 'Tc9oNX8EuNQohWVDZeaPOHmeBU5tuwVdwIRyfltnTPw=',
                )
            ]
        );

        $signature = new Signature(
            SignedInfo: $signInfo,
            SignatureValue: 'kZhLB843E/sJEd66jI1lcfRheCZXaaHs9EjYOktMy9f/QmK7f4rFKcK24lqdcr+upqNbgRBJy3ahPnEv/AMb+ncklAkkxj2bOeVtUhi3wgh7pF0UUFoGFGb49sHRf9wEJ/IMMhiCs+weOSzVUCPiUGszFxwfyDps+ft5ZEKU3m1pIGcbu7V3qv7iNBkYtdfkFXbDxLBcOwGrJpXJ9/QYPmQrsEG0ROJV4Jhjb8R+X7T6K9UZlV/ciUXURO6AKzU4uHThPmcveHZWAxZqpmQEk2zelqsVGRAMformANhoXnWO4JxzSriQMnk5Mglu6hiapwEQMHySz7L0ib/Yp23RTw==',
            KeyInfo: new KeyInfo(
                X509Data: new X509Data(
                    X509Certificate: 'MIIFlDCCA3ygAwIBAgIQeomZorO+0AwmW2BRdWJMxTANBgkqhkiG9w0BAQsFADB1MQswCQYDVQQGEwJNWTEOMAwGA1UEChMFTEhETk0xNjA0BgNVBAsTLVRlcm1zIG9mIHVzZSBhdCBodHRwOi8vd3d3LnBvc2RpZ2ljZXJ0LmNvbS5teTEeMBwGA1UEAxMVVHJpYWwgTEhETk0gU3ViIENBIFYxMB4XDTI0MDYwNjAyNTIzNloXDTI0MDkwNjAyNTIzNlowgZwxCzAJBgNVBAYTAk1ZMQ4wDAYDVQQKEwVEdW1teTEVMBMGA1UEYRMMQzI5NzAyNjM1MDYwMRswGQYDVQQLExJUZXN0IFVuaXQgZUludm9pY2UxDjAMBgNVBAMTBUR1bW15MRIwEAYDVQQFEwlEMTIzNDU2NzgxJTAjBgkqhkiG9w0BCQEWFmFuYXMuYUBmZ3Zob2xkaW5ncy5jb20wggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQChvfOzAofnU60xFO7NcmF2WRi+dgor1D7ccISgRVfZC30Fdxnt1S6ZNf78Lbrz8TbWMicS8plh/pHy96OJvEBplsAgcZTd6WvaMUB2oInC86D3YShlthR6EzhwXgBmg/g9xprwlRqXMT2p4+K8zmyJZ9pIb8Y+tQNjm/uYNudtwGVm8A4hEhlRHbgfUXRzT19QZml6V2Ea0wQI8VyWWa8phCIkBD2w4F8jG4eP5/0XSQkTfBHHf+GV/YDJx5KiiYfmB1nGfwoPHix6Gey+wRjIq87on8Dm5+8ei8/bOhcuuSlpxgwphAP3rZrNbRN9LNVLSQ5md41asoBHfaDIVPVpAgMBAAGjgfcwgfQwHwYDVR0lBBgwFgYIKwYBBQUHAwQGCisGAQQBgjcKAwwwEQYDVR0OBAoECEDwms66hrpiMFMGA1UdIARMMEowSAYJKwYBBAGDikUBMDswOQYIKwYBBQUHAgEWLWh0dHBzOi8vd3d3LnBvc2RpZ2ljZXJ0LmNvbS5teS9yZXBvc2l0b3J5L2NwczATBgNVHSMEDDAKgAhNf9lrtsUI0DAOBgNVHQ8BAf8EBAMCBkAwRAYDVR0fBD0wOzA5oDegNYYzaHR0cDovL3RyaWFsY3JsLnBvc2RpZ2ljZXJ0LmNvbS5teS9UcmlhbExIRE5NVjEuY3JsMA0GCSqGSIb3DQEBCwUAA4ICAQBwptnIb1OA8NNVotgVIjOnpQtowew87Y0EBWAnVhOsMDlWXD/s+BL7vIEbX/BYa0TjakQ7qo4riSHyUkQ+X+pNsPEqolC4uFOp0pDsIdjsNB+WG15itnghkI99c6YZmbXcSFw9E160c7vG25gIL6zBPculHx5+laE59YkmDLdxx27e0TltUbFmuq3diYBOOf7NswFcDXCo+kXOwFfgmpbzYS0qfSoh3eZZtVHg0r6uga1UsMGb90+IRsk4st99EOVENvo0290lWhPBVK2G34+2TzbbYnVkoxnq6uDMw3cRpXX/oSfya+tyF51kT3iXvpmQ9OMF3wMlfKwCS7BZB2+iRja/1WHkAP7QW7/+0zRBcGQzY7AYsdZUllwYapsLEtbZBrTiH12X4XnZjny9rLfQLzJsFGT7Q+e02GiCsBrK7ZHNTindLRnJYAo4U2at5+SjqBiXSmz0DG+juOyFkwiWyD0xeheg4tMMO2pZ7clQzKflYnvFTEFnt+d+tvVwNjTboxfVxEv2qWF6qcMJeMvXwKTXuwVI2iUqmJSzJbUY+w3OeG7fvrhUfMJPM9XZBOp7CEI1QHfHrtyjlKNhYzG3IgHcfAZUURO16gFmWgzAZLkJSmCIxaIty/EmvG5N3ZePolBOa7lNEH/eSBMGAQteH+Twtiu0Y2xSwmmsxnfJyw='
                )
            ),
            Object: $object
        );

        $UBLExtensions = new UBLExtensions(
            UBLExtension: new UBLExtension(
                ExtensionContent: new ExtensionContent(
                    UBLDocumentSignatures: new UBLDocumentSignatures(
                        SignatureInformation: new SignatureInformation(
                            ID: 'urn:oasis:names:specification:ubl:signature:1',
                            ReferencedSignatureID: 'urn:oasis:names:specification:ubl:signature:Invoice',
                            Signature: $signature,
                        )
                    ),
                )
            ),
        );

        // $content = $this->writeXml($service, 'UBLExtensions', $UBLExtensions->toXmlArray());
        // $this->displayXml($content);

        return $UBLExtensions;
    }

    private function loadSignature2(mixed $data)
    {
        $extService = new Service();
        $sigService = new Service();

        $extService->namespaceMap = [
            XMLNS::EXT->getNamespace() => '',
        ];

        $sigService->namespaceMap = [
            XMLNS::SIG->getNamespace() => XMLNS::SIG(),
            XMLNS::SAC->getNamespace() => XMLNS::SAC(),
            XMLNS::SBC->getNamespace() => XMLNS::SBC(),
        ];

        $UBLDocumentSignatures = new UBLDocumentSignatures(new SignatureInformation());
        $sigData = $sigService->write(XMLNS::SIG() . ':UBLDocumentSignatures', $UBLDocumentSignatures);

        // create UBLDocumentSignatures xml
        $sigDom = $this->createDOM();
        $sigDom->loadXML($sigData);


        $extContentDom = $this->createDOM();
        $extContentElement = $extContentDom->createElement('ExtensionContent');
        $newNode2 = $extContentDom->appendChild($extContentElement);
        $newNode1 = $extContentDom->importNode($sigDom->documentElement, true);
        $newNode2->appendChild($newNode1);

        $UBLExtensions = new UBLExtensions(new UBLExtension());
        $extData = $extService->write('UBLExtensions', $UBLExtensions);

        // create UBLExtensions xml
        $extDom = $this->createDOM();
        $extDom->loadXML($extData);

        // header("Content-type: text/xml");
        // echo $extDom->saveXML();
        // exit;

        $parentPath = "//ns:UBLExtensions/ns:UBLExtension";

        $xpath = new \DomXPath($extDom);
        $xpath->registerNameSpace('ns', XMLNS::EXT->getNamespace());
        $parent = $xpath->query($parentPath);


        $newNode = $extDom->importNode($newNode2, true);
        $parent->item(0)->append($newNode);

        // header("Content-type: text/xml");
        // echo $extDom->saveXML();
        // exit;

        return $extDom;
    }

    public function generateJSONDocument($data)
    {

    }

    private function createDOM(
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

    private function writeXml(Service $service, $rootElement = '', array $xml = []): string
    {
        $xmlData = $service->write($rootElement, $xml);

        $dom = $this->createDOM();
        $dom->loadXML($xmlData);

        return $dom->saveXML();
    }

    private function displayXml(string $xml)
    {
        header("Content-type: text/xml");
        echo $xml;
        exit;
    }

    private function removeUnusedAttributes(DOMDocument $dom, string $tagName, array $excepts = [])
    {
        $extensionContent = $dom->getElementsByTagName($tagName);
        $extensionContentAttrs = $extensionContent?->item(0)?->getAttributeNames();

        if (is_array($extensionContentAttrs) && count($extensionContentAttrs) > 0) {
            foreach ($extensionContentAttrs as $extensionContentAttr) {
                if (!in_array($extensionContentAttr, $excepts)) {
                    $extensionContent->item(0)->removeAttribute($extensionContentAttr);
                }

            }
        }
    }

    private function cleanUp(DOMDocument $dom)
    {
        $this->removeUnusedAttributes(dom: $dom, tagName: 'SignatureInformation');
        $this->removeUnusedAttributes(dom: $dom, tagName: 'ExtensionContent');
        $this->removeUnusedAttributes(dom: $dom, tagName: 'UBLExtensions', excepts: ['xmlns']);

        $content = $dom->saveXML();
        $content = str($content)
            ->replace('UBLDocumentSignatures', 'sig:UBLDocumentSignatures')
            ->value;

        return $content;
    }

}
