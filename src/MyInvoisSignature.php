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

    private ?MyInvoisCertificate $certificate = null;

    private ?MyInvoisHelper $helper = null;


    private $hashAlgorithm = 'sha256';

    private $signAlgorithm = OPENSSL_ALGO_SHA256;

    public bool $hasSignature = false;

    private ?string $docDigest = null;

    private ?string $propsDigest = null;

    private ?string $certDigest = null;

    private ?string $sig = null;

    private ?Carbon $signingTime = null;

    private ?string $issuerName = null;

    private ?string $serialNumber = null;

    public function __construct(
        public Invoice $document,
        public ?string $certificatePath = null,
        public ?string $privateKeyPath = null,
        public ?string $passphrase = null,
    ) {
        $this->helper = new MyInvoisHelper();
        $this->checkCertFiles();
        $this->prepare();
        $this->build(); // Step 8
    }

    private function prepare()
    {
        $this->certificate = $this->getCertData(); // MyInvoisCertificate       

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
        $certHash = $this->hashContent(content: $this->certificate?->getRawCertificate(), binary: true);
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
            ))->add('attributes', ['Algorithm' => 'http://www.w3.org/TR/1999/REC-xpath-19991116']),
            (new Transform(
                XPath: 'not(//ancestor-or-self::cac:Signature)'
            ))->add('attributes', ['Algorithm' => 'http://www.w3.org/TR/1999/REC-xpath-19991116']),
            (new Transform())->add('attributes', ['Algorithm' => $xmlCanonicalizationURI]),
        ];

        $references = [
            (new Reference(
                Transforms: new Transforms(Transform: $transforms),
                DigestMethod: new Data('', ['Algorithm' => $xmlEncAlgo]),
                DigestValue: $this->docDigest,
            ))->add('attributes', ['Id' => 'id-doc-signed-data', 'URI' => '']),
            (new Reference(
                DigestMethod: new Data('', ['Algorithm' => $xmlEncAlgo]),
                DigestValue: $this->propsDigest,
            ))->add('attributes', ['Type' => 'http://www.w3.org/2000/09/xmldsig#SignatureProperties', 'URI' => '#id-xades-signed-props']),
        ];

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
        ))->add('attributes', ['xmlns:' . XMLNS::DS() => XMLNS::DS->getNamespace(), 'Id' => 'signature']);

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

        $cert = new Cert(
            CertDigest: new CertDigest(
                DigestMethod: new Data('', ['Algorithm' => $xmlEncAlgo]),
                DigestValue: $this->certDigest,
            ),
            IssuerSerial: new IssuerSerial(
                X509IssuerName: $this->issuerName,
                X509SerialNumber: $this->serialNumber
            )
        );

        return new SignedProperties(
            new SignedSignatureProperties(
                SigningTime: $this->signingTime?->toIso8601ZuluString(),
                SigningCertificate: new SigningCertificate(
                    Cert: $cert
                ),
            )
        );
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

        $xml = $this->helper->writeXml($service, 'root', $this->getQualifyingProperties()?->toXmlArray());
        $xml = $this->helper->removeXMLTag($xml);

        $dom = $this->helper->createDOM();
        $dom->loadXML($xml);
        $content = $dom->C14N();

        $regex = "#<\s*?root\b[^>]*>(.*?)</root\b[^>]*>#"; // Remove the root node and only get the SignedProperties node 
        preg_match($regex, $content, $matches);

        return data_get($matches, 1);
    }

    private function getCertData(): MyInvoisCertificate
    {
        $certContent = file_get_contents($this->certificatePath);
        $privateKeyContent = null;
        $ext = pathinfo($this->certificatePath, PATHINFO_EXTENSION);

        if ($ext === 'p12' || $ext === 'pfx') {
            if (!openssl_pkcs12_read($certContent, $certs, $this->passphrase)) {
                throw new MyInvoisException('OpenSSL Error: ' . openssl_error_string() ?? 'Invalid cetificate');
            }

            $certContent = data_get($certs, 'cert');
            $privateKeyContent = data_get($certs, 'pkey');
        } else {
            $privateKeyContent = file_get_contents($this->privateKeyPath);
        }

        $certInfo = openssl_x509_parse($certContent);

        return new MyInvoisCertificate(
            certificate: $certContent,
            privateKey: $privateKeyContent,
            info: $certInfo
        );
    }

    private function signDocumentDigest(string $content): string
    {
        $privateKey = $this->certificate?->getPrivateKey();

        openssl_sign($content, $signature, $privateKey, $this->signAlgorithm);

        return $signature;
    }

    private function checkCertFiles()
    {
        if (!$this->isFileExists($this->certificatePath)) {
            $this->certificatePath = null;
        }

        if (!$this->isFileExists($this->privateKeyPath)) {
            $this->privateKeyPath = null;
        }

        if ($this->certificatePath && $this->privateKeyPath) {
            $this->hasSignature = true;
        }

        if ($this->hasSignature === false) {
            throw new MyInvoisException(__('Missing certificate and private key'));
        }
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