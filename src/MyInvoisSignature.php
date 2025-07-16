<?php

namespace Laraditz\MyInvois;

use Illuminate\Support\Carbon;
use Laraditz\MyInvois\Data\Cert;
use Laraditz\MyInvois\Data\Data;
use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Data\Invoice;
use Laraditz\MyInvois\Data\KeyInfo;
use Laraditz\MyInvois\Data\X509Data;
use Laraditz\MyInvois\Data\Reference;
use Laraditz\MyInvois\Data\Signature;
use Laraditz\MyInvois\Data\Transform;
use Laraditz\MyInvois\Data\CertDigest;
use Laraditz\MyInvois\Data\DataObject;
use Laraditz\MyInvois\Data\SignedInfo;
use Laraditz\MyInvois\Data\Transforms;
use Laraditz\MyInvois\Data\IssuerSerial;
use Laraditz\MyInvois\Data\UBLExtension;
use Laraditz\MyInvois\Data\UBLExtensions;
use Laraditz\MyInvois\Data\ExtensionContent;
use Laraditz\MyInvois\Data\SignedProperties;
use Laraditz\MyInvois\Data\SigningCertificate;
use Laraditz\MyInvois\Data\QualifyingProperties;
use Laraditz\MyInvois\Data\SignatureInformation;
use Laraditz\MyInvois\Data\UBLDocumentSignatures;
use Laraditz\MyInvois\Exceptions\MyInvoisException;
use Laraditz\MyInvois\Data\SignedSignatureProperties;

/**
 * Ref: https://sdk.myinvois.hasil.gov.my/signature-creation/
 */
class MyInvoisSignature
{
    private UBLExtensions $UBLExtensions;

    private Signature $signature;

    private ?MyInvoisHelper $helper = null;

    private $hashAlgorithm = 'sha256';

    private $signAlgorithm = OPENSSL_ALGO_SHA256;

    private ?string $docDigest = null;

    private ?string $propsDigest = null;

    private ?string $certDigest = null;

    private ?string $sig = null;

    private ?Carbon $signingTime = null;

    private ?string $issuerName = null;

    private ?string $serialNumber = null;

    public function __construct(
        public Invoice $document,
        private MyInvoisCertificate $certificate,
    ) {
        $this->helper = new MyInvoisHelper();
        $this->prepare();
        $this->build(); // Step 8
    }

    private function prepare()
    {
        // Step 2: Apply transformations to the document
        $service = $this->helper->createInvoiceXMLService();
        $xml = $this->helper->writeXml($service, 'Invoice', $this->document->toXmlArray());
        $xml = $this->helper->removeXMLTag($xml);
        // $this->helper->displayXml($xml);

        // Step 3: Canonicalize the document and generate the document hash (digest)
        $dom = $this->helper->createDOM();
        $dom->loadXML($xml);
        $docContent = $dom->C14N();
        $this->docDigest = base64_encode($this->hashContent(content: $docContent, binary: true));

        // Step 4: Sign the document digest
        $sig = $this->signDocumentDigest($docContent);
        $this->sig = base64_encode($sig);

        // Step 5: Generate the certificate hash     
        $decodedCert = base64_decode($this->certificate?->getRawCertificate());
        $certHash = $this->hashContent(content: $decodedCert, binary: true);
        $this->certDigest = base64_encode($certHash);

        // Step 6: Populate the signed properties section
        $this->signingTime = now()->setTimezone('UTC');
        $this->issuerName = $this->certificate?->getIssuerName();
        $this->serialNumber = $this->certificate?->getInfo('serialNumber');

        // Step 7: Generate Signed Properties Hash
        $signedPropertiesContent = $this->getSignedPropertiesContent();
        $signedPropertiesContentHash = $this->hashContent($signedPropertiesContent, binary: true);
        $this->propsDigest = base64_encode($signedPropertiesContentHash);

        // Step 8: Populate the information in the document to create the signed document
        // build the object by calling $this->build()
    }

    private function build()
    {
        $signatureID = 'urn:oasis:names:specification:ubl:signature:Invoice';
        $signatureMethod = 'urn:oasis:names:specification:ubl:dsig:enveloped:xades';
        $xmlEncAlgo = $this->getXmlEncAlgo();
        $xmlCanonicalizationURI = 'http://www.w3.org/2001/10/xml-exc-c14n#';

        $transforms = [
            (new Transform(
                XPath: 'not(//ancestor-or-self::ext:UBLExtensions)'
            ))->set('attributes', ['Algorithm' => 'http://www.w3.org/TR/1999/REC-xpath-19991116']),
            (new Transform(
                XPath: 'not(//ancestor-or-self::cac:Signature)'
            ))->set('attributes', ['Algorithm' => 'http://www.w3.org/TR/1999/REC-xpath-19991116']),
            (new Transform())->set('attributes', ['Algorithm' => $xmlCanonicalizationURI]),
        ];

        $references = [
            (new Reference(
                Transforms: new Transforms(Transform: $transforms),
                DigestMethod: new Data('', ['Algorithm' => $xmlEncAlgo]),
                DigestValue: $this->docDigest,
            ))->set('attributes', ['Id' => 'id-doc-signed-data', 'URI' => '']),
            (new Reference(
                DigestMethod: new Data('', ['Algorithm' => $xmlEncAlgo]),
                DigestValue: $this->propsDigest,
            ))->set('attributes', ['Type' => 'http://uri.etsi.org/01903/v1.3.2#SignedProperties', 'URI' => '#id-xades-signed-props']),
        ]; //Why in LHDN sample Type=http://www.w3.org/2000/09/xmldsig#SignatureProperties ?

        $signInfo = new SignedInfo(
            CanonicalizationMethod: new Data('', ['Algorithm' => $xmlCanonicalizationURI]),
            SignatureMethod: new Data('', ['Algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256']),
            Reference: $references
        );

        $signature = (new Signature(
            SignedInfo: $signInfo,
            SignatureValue: $this->sig,
            KeyInfo: new KeyInfo(
                X509Data: new X509Data(
                    X509Certificate: $this->certificate?->getRawCertificate()
                )
            ),
            Object: new DataObject(
                QualifyingProperties: $this->getQualifyingProperties()
            )
        ))->set('attributes', ['xmlns:' . XMLNS::DS() => XMLNS::DS->getNamespace(), 'Id' => 'signature']);

        $UBLExtensions = new UBLExtensions(
            UBLExtension: new UBLExtension(
                ExtensionURI: $signatureMethod,
                ExtensionContent: new ExtensionContent(
                    UBLDocumentSignatures: new UBLDocumentSignatures(
                        SignatureInformation: new SignatureInformation(
                            ID: 'urn:oasis:names:specification:ubl:signature:1',
                            ReferencedSignatureID: $signatureID,
                            Signature: $signature,
                        )
                    ),
                )
            ),
        );

        $signature = new Signature(
            ID: $signatureID,
            SignatureMethod: $signatureMethod
        );

        // $content = $this->helper->writeXml($service, 'UBLExtensions', $UBLExtensions->toXmlArray());
        // $this->helper->displayXml($content);

        $this->setUBLExtensions($UBLExtensions);
        $this->setSignature($signature);
    }

    private function getSignedProperties(): SignedProperties
    {
        $xmlEncAlgo = $this->getXmlEncAlgo();
        $digestMethodAttributes = ['Algorithm' => $xmlEncAlgo];

        $cert = new Cert(
            CertDigest: new CertDigest(
                DigestMethod: new Data('', $digestMethodAttributes),
                DigestValue: new Data($this->certDigest),
            ),
            IssuerSerial: new IssuerSerial(
                X509IssuerName: new Data($this->issuerName),
                X509SerialNumber: new Data($this->serialNumber)
            )
        );

        $SignedSignatureProperties = new SignedSignatureProperties(
            SigningTime: $this->signingTime?->toIso8601ZuluString(),
            SigningCertificate: new SigningCertificate(
                Cert: $cert
            ),
        );

        return new SignedProperties($SignedSignatureProperties);
    }

    private function getQualifyingProperties(): QualifyingProperties
    {
        return new QualifyingProperties(
            SignedProperties: $this->getSignedProperties()
        );
    }

    private function getXmlEncURI()
    {
        return 'http://www.w3.org/2001/04/xmlenc#';
    }

    private function getXmlEncAlgo()
    {
        return $this->getXmlEncURI() . $this->hashAlgorithm;
    }

    private function getSignedPropertiesContent(): ?string
    {
        $service = $this->helper->createQualifyingPropertiesXMLService();
        $xml = $service->write('root', $this->getQualifyingProperties()?->toXmlArray());

        $dom = $this->helper->createDOM();
        $dom->loadXML($xml, LIBXML_NOEMPTYTAG);

        $content = $dom->C14N(exclusive: true, nsPrefixes: [XMLNS::XADES->getNamespace(), XMLNS::DS->getNamespace()]);
        // $this->helper->displayXml($content);

        // attributes need to be in this sequence. Got a better way?
        $content = str_replace('xmlns:xades="http://uri.etsi.org/01903/v1.3.2#" Id="id-xades-signed-props"', 'Id="id-xades-signed-props" xmlns:xades="http://uri.etsi.org/01903/v1.3.2#"', $content);
        $content = str_replace('xmlns:ds="http://www.w3.org/2000/09/xmldsig#" Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"', 'Algorithm="http://www.w3.org/2001/04/xmlenc#sha256" xmlns:ds="http://www.w3.org/2000/09/xmldsig#"', $content);

        $regex = "#<\s*?root\b[^>]*>(.*?)</root\b[^>]*>#"; // Remove the root node and only get the SignedProperties node 
        preg_match($regex, $content, $matches);

        $signedPropertiesContent = data_get($matches, 1);

        return $signedPropertiesContent;
    }

    private function signDocumentDigest(string $content): string
    {
        $privateKey = $this->certificate?->getPrivateKey();

        openssl_sign($content, $signature, $privateKey, $this->signAlgorithm);

        return $signature;
    }

    public function setUBLExtensions(UBLExtensions $UBLExtensions): void
    {
        $this->UBLExtensions = $UBLExtensions;
    }

    public function getUBLExtensions(): UBLExtensions
    {
        return $this->UBLExtensions;
    }

    public function setSignature(Signature $signature): void
    {
        $this->signature = $signature;
    }

    public function getSignature(): Signature
    {
        return $this->signature;

    }

    private function isFileExists(string $path): bool
    {
        if ($path && is_file($path) && file_exists($path)) {
            return true;
        }

        return false;
    }

    public function hashContent(string $content, bool $binary = false): string
    {
        return hash($this->hashAlgorithm, $content, $binary);
    }
}