<?php

namespace Laraditz\MyInvois;

use DOMDocument;
use LogicException;
use Sabre\Xml\Service;
use BadMethodCallException;
use Illuminate\Support\Str;
use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Enums\Format;
use Laraditz\MyInvois\Data\UBLExtension;
use Laraditz\MyInvois\Data\UBLExtensions;
use Laraditz\MyInvois\Data\ExtensionContent;
use Laraditz\MyInvois\Data\SignatureInformation;
use Laraditz\MyInvois\Data\UBLDocumentSignatures;
use Laraditz\MyInvois\Models\MyinvoisAccessToken;
use Laraditz\MyInvois\Exceptions\MyInvoisApiError;

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

        // $data->add('UBLExtensions', new Data($signature, ['xmlns' => XMLNS::EXT->getNamespace()]));
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

    private function loadSignature(mixed $data)
    {
        $service = new Service();
        // $service->namespaceMap = [
        //     XMLNS::EXT->getNamespace() => '',
        //     // XMLNS::SIG->getNamespace() => XMLNS::SIG(),
        //     // XMLNS::SAC->getNamespace() => XMLNS::SAC(),
        //     // XMLNS::SBC->getNamespace() => XMLNS::SBC(),
        // ];

        // $UBLDocumentSignatures = new UBLDocumentSignatures(new SignatureInformation());

        $UBLExtensions = new UBLExtensions(
            UBLExtension: new UBLExtension(
                ExtensionContent: new ExtensionContent(
                    UBLDocumentSignatures: new UBLDocumentSignatures(
                        SignatureInformation: new SignatureInformation()
                    ),
                )
            ),
        );

        // $content = $this->writeXml($service, 'UBLExtensions', $UBLExtensions->toXmlArray());
        // $this->displayXml($content);

        return $UBLExtensions;
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
}
