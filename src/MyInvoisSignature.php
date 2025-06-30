<?php

namespace Laraditz\MyInvois;

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
use Laraditz\MyInvois\Data\SignedSignatureProperties;

class MyInvoisSignature
{
    private UBLExtensions $UBLExtensions;
    private Signature $signature;

    public function __construct(
        public Invoice $document,
    ) {
        $this->build();
    }

    public function build()
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

        $transforms = [
            (new Transform(
                XPath: 'not(//ancestor-or-self::ext:UBLExtensions)'
            ))->add('attributes', ['Algorithm' => 'http://www.w3.org/TR/1999/REC-xpath-19991116']),
            (new Transform(
                XPath: 'not(//ancestor-or-self::cac:Signature)'
            ))->add('attributes', ['Algorithm' => 'http://www.w3.org/TR/1999/REC-xpath-19991116']),
            (new Transform())->add('attributes', ['Algorithm' => 'http://www.w3.org/2001/10/xml-exc-c14n#']),
        ];

        $references = [
            (new Reference(
                Transforms: new Transforms(Transform: $transforms),
                DigestMethod: new Data('', ['Algorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256']),
                DigestValue: 'fRaWJINS9sB9aSl/MhCjMsdVMFpLwnxstpPhJkJwkU4=',
            ))->add('attributes', ['Id' => 'id-doc-signed-data', 'URI' => '']),
            (new Reference(
                DigestMethod: new Data('', ['Algorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256']),
                DigestValue: 'Tc9oNX8EuNQohWVDZeaPOHmeBU5tuwVdwIRyfltnTPw=',
            ))->add('attributes', ['Type' => 'http://www.w3.org/2000/09/xmldsig#SignatureProperties', 'URI' => '#id-xades-signed-props']),
        ];

        $signInfo = new SignedInfo(
            CanonicalizationMethod: new Data('', ['Algorithm' => 'http://www.w3.org/2001/10/xml-exc-c14n#']),
            SignatureMethod: new Data('', ['Algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256']),
            Reference: $references
        );

        $signature = (new Signature(
            SignedInfo: $signInfo,
            SignatureValue: 'kZhLB843E/sJEd66jI1lcfRheCZXaaHs9EjYOktMy9f/QmK7f4rFKcK24lqdcr+upqNbgRBJy3ahPnEv/AMb+ncklAkkxj2bOeVtUhi3wgh7pF0UUFoGFGb49sHRf9wEJ/IMMhiCs+weOSzVUCPiUGszFxwfyDps+ft5ZEKU3m1pIGcbu7V3qv7iNBkYtdfkFXbDxLBcOwGrJpXJ9/QYPmQrsEG0ROJV4Jhjb8R+X7T6K9UZlV/ciUXURO6AKzU4uHThPmcveHZWAxZqpmQEk2zelqsVGRAMformANhoXnWO4JxzSriQMnk5Mglu6hiapwEQMHySz7L0ib/Yp23RTw==',
            KeyInfo: new KeyInfo(
                X509Data: new X509Data(
                    X509Certificate: 'MIIFlDCCA3ygAwIBAgIQeomZorO+0AwmW2BRdWJMxTANBgkqhkiG9w0BAQsFADB1MQswCQYDVQQGEwJNWTEOMAwGA1UEChMFTEhETk0xNjA0BgNVBAsTLVRlcm1zIG9mIHVzZSBhdCBodHRwOi8vd3d3LnBvc2RpZ2ljZXJ0LmNvbS5teTEeMBwGA1UEAxMVVHJpYWwgTEhETk0gU3ViIENBIFYxMB4XDTI0MDYwNjAyNTIzNloXDTI0MDkwNjAyNTIzNlowgZwxCzAJBgNVBAYTAk1ZMQ4wDAYDVQQKEwVEdW1teTEVMBMGA1UEYRMMQzI5NzAyNjM1MDYwMRswGQYDVQQLExJUZXN0IFVuaXQgZUludm9pY2UxDjAMBgNVBAMTBUR1bW15MRIwEAYDVQQFEwlEMTIzNDU2NzgxJTAjBgkqhkiG9w0BCQEWFmFuYXMuYUBmZ3Zob2xkaW5ncy5jb20wggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQChvfOzAofnU60xFO7NcmF2WRi+dgor1D7ccISgRVfZC30Fdxnt1S6ZNf78Lbrz8TbWMicS8plh/pHy96OJvEBplsAgcZTd6WvaMUB2oInC86D3YShlthR6EzhwXgBmg/g9xprwlRqXMT2p4+K8zmyJZ9pIb8Y+tQNjm/uYNudtwGVm8A4hEhlRHbgfUXRzT19QZml6V2Ea0wQI8VyWWa8phCIkBD2w4F8jG4eP5/0XSQkTfBHHf+GV/YDJx5KiiYfmB1nGfwoPHix6Gey+wRjIq87on8Dm5+8ei8/bOhcuuSlpxgwphAP3rZrNbRN9LNVLSQ5md41asoBHfaDIVPVpAgMBAAGjgfcwgfQwHwYDVR0lBBgwFgYIKwYBBQUHAwQGCisGAQQBgjcKAwwwEQYDVR0OBAoECEDwms66hrpiMFMGA1UdIARMMEowSAYJKwYBBAGDikUBMDswOQYIKwYBBQUHAgEWLWh0dHBzOi8vd3d3LnBvc2RpZ2ljZXJ0LmNvbS5teS9yZXBvc2l0b3J5L2NwczATBgNVHSMEDDAKgAhNf9lrtsUI0DAOBgNVHQ8BAf8EBAMCBkAwRAYDVR0fBD0wOzA5oDegNYYzaHR0cDovL3RyaWFsY3JsLnBvc2RpZ2ljZXJ0LmNvbS5teS9UcmlhbExIRE5NVjEuY3JsMA0GCSqGSIb3DQEBCwUAA4ICAQBwptnIb1OA8NNVotgVIjOnpQtowew87Y0EBWAnVhOsMDlWXD/s+BL7vIEbX/BYa0TjakQ7qo4riSHyUkQ+X+pNsPEqolC4uFOp0pDsIdjsNB+WG15itnghkI99c6YZmbXcSFw9E160c7vG25gIL6zBPculHx5+laE59YkmDLdxx27e0TltUbFmuq3diYBOOf7NswFcDXCo+kXOwFfgmpbzYS0qfSoh3eZZtVHg0r6uga1UsMGb90+IRsk4st99EOVENvo0290lWhPBVK2G34+2TzbbYnVkoxnq6uDMw3cRpXX/oSfya+tyF51kT3iXvpmQ9OMF3wMlfKwCS7BZB2+iRja/1WHkAP7QW7/+0zRBcGQzY7AYsdZUllwYapsLEtbZBrTiH12X4XnZjny9rLfQLzJsFGT7Q+e02GiCsBrK7ZHNTindLRnJYAo4U2at5+SjqBiXSmz0DG+juOyFkwiWyD0xeheg4tMMO2pZ7clQzKflYnvFTEFnt+d+tvVwNjTboxfVxEv2qWF6qcMJeMvXwKTXuwVI2iUqmJSzJbUY+w3OeG7fvrhUfMJPM9XZBOp7CEI1QHfHrtyjlKNhYzG3IgHcfAZUURO16gFmWgzAZLkJSmCIxaIty/EmvG5N3ZePolBOa7lNEH/eSBMGAQteH+Twtiu0Y2xSwmmsxnfJyw='
                )
            ),
            Object: $object
        ))->add('attributes', ['xmlns:' . XMLNS::DS() => XMLNS::DS->getNamespace(), 'Id' => 'signature']);

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

        $signature = new Signature(
            ID: 'urn:oasis:names:specification:ubl:signature:Invoice',
            SignatureMethod: 'urn:oasis:names:specification:ubl:dsig:enveloped:xades'
        );

        // $content = $this->writeXml($service, 'UBLExtensions', $UBLExtensions->toXmlArray());
        // $this->displayXml($content);

        $this->setUBLExtensions($UBLExtensions);
        $this->setSignature($signature);
    }

    public function setUBLExtensions(UBLExtensions $UBLExtensions): void
    {
        $this->UBLExtensions = $UBLExtensions;
    }

    public function getUBLExtensions(): UBLExtensions
    {
        return $this->UBLExtensions;
    }

    public function setSignature(Signature $signature)
    {
        $this->signature = $signature;
    }

    public function getSignature(): Signature
    {
        return $this->signature;

    }


}