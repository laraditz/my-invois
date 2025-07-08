<?php

namespace Laraditz\MyInvois;

use DOMDocument;
use Laraditz\MyInvois\Data\Invoice;
use LogicException;
use Sabre\Xml\Service;
use BadMethodCallException;
use Illuminate\Support\Str;
use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Enums\Format;
use Laraditz\MyInvois\Models\MyinvoisAccessToken;
use Laraditz\MyInvois\Exceptions\MyInvoisApiError;

class MyInvois
{
    private $services = ['auth', 'document_type', 'taxpayer', 'notification', 'document'];

    private $hashAlgorithm = 'sha256';

    public function __construct(
        private string $client_id,
        private string $client_secret,
        private bool $is_sandbox = false,
        private ?string $certificate_path = null,
        private ?string $private_key_path = null,
        private ?string $passphrase = null,
        private ?string $disk = 'local',
        private ?string $document_path = null,
    ) {
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

            $accessToken = data_get($myinvois, 'data.access_token');

            if (!$accessToken) {
                throw new MyInvoisApiError($result ?? ['code' => __('Missing an access token.')]);
            }

            return $accessToken;
        }
    }

    public function generateDocument(Invoice $data, Format $format)
    {
        return match ($format) {
            Format::XML => $this->generateXMLDocument(data: $data),
            Format::JSON => $this->generateJSONDocument(data: $data),
        };
    }

    public function generateXMLDocument(Invoice $data)
    {
        $hasSignature = false;
        $helper = new MyInvoisHelper();
        $service = $helper->createInvoiceXMLService();

        if (
            $this->isFileExists($this->getCertificatePath())
            && $this->isFileExists($this->getPrivateKeyPath())
        ) {
            $hasSignature = true;
        }

        if ($hasSignature === true) {
            // add signature to document
            $sig = new MyInvoisSignature(
                document: $data,
                certificatePath: $this->getCertificatePath(),
                privateKeyPath: $this->getPrivateKeyPath(),
                passphrase: $this->getPassphrase()
            );

            $data->add('UBLExtensions', $sig->getUBLExtensions())
                ->add('Signature', $sig->getSignature());
        }

        $content = $helper->writeXml($service, 'Invoice', $data->toXmlArray());
        // $helper->displayXml($content);

        return $content;
    }

    // in progress
    public function generateJSONDocument(Invoice $data)
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
        return $this->is_sandbox;
    }

    public function getCertificatePath(): ?string
    {
        return $this->certificate_path;
    }

    public function getPrivateKeyPath(): ?string
    {
        return $this->private_key_path;
    }

    public function getPassphrase(): ?string
    {
        return $this->passphrase;
    }

    public function getHashAlgorithm(): string
    {
        return $this->hashAlgorithm;
    }

    public function getDocumentPath(): string
    {
        return $this->document_path;
    }

    public function getDisk(): string
    {
        return $this->disk;
    }

    public function config(string $name): array|string|int|bool
    {
        return config('myinvois.' . $name);
    }

    private function isFileExists(string $path): bool
    {
        if ($path && is_file($path) && file_exists($path)) {
            return true;
        }

        return false;
    }

    public function helper(): MyInvoisHelper
    {
        return new MyInvoisHelper;
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

    // old code, will remove?
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
