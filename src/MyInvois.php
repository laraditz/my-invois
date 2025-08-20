<?php

namespace Laraditz\MyInvois;

use DOMDocument;
use LogicException;
use BadMethodCallException;
use Illuminate\Support\Str;
use Laraditz\MyInvois\Data\Invoice;
use Laraditz\MyInvois\Enums\Format;
use Laraditz\MyInvois\Models\MyinvoisAccessToken;
use Laraditz\MyInvois\Exceptions\MyInvoisApiError;
use Laraditz\MyInvois\Exceptions\MyInvoisException;

class MyInvois
{
    private $services = ['auth', 'document_type', 'taxpayer', 'notification', 'document', 'document_submission'];

    private $hashAlgorithm = 'sha256';

    private bool $hasSignature = false;

    private ?MyInvoisCertificate $certificate = null;

    public function __construct(
        private ?bool $is_sandbox = null,
        private ?string $client_id = null,
        private ?string $client_secret = null,
        private ?string $certificate_path = null,
        private ?string $private_key_path = null,
        private ?string $passphrase = null,
        private ?string $disk = null,
        private ?string $document_path = null,
        private ?string $on_behalf_of = null,
    ) {
        $this->setClientId($this->client_id ?? $this->config('client_id'));
        $this->setClientSecret($this->client_secret ?? $this->config('client_secret'));
        $this->setIsSandbox($this->is_sandbox ?? $this->config('sandbox.mode'));
        $this->setCertificatePath($this->certificate_path ?? $this->config('certificate_path'));
        $this->setPrivateKeyPath($this->private_key_path ?? $this->config('private_key_path'));
        $this->setPassphrase($this->passphrase ?? $this->config('passphrase'));
        $this->setDisk($this->disk ?? $this->config('disk') ?? config('filesystems.default'));
        $this->setDocumentPath($this->document_path ?? $this->config('document_path'));
        $this->setOnBehalfOf($this->on_behalf_of ?? $this->config('on_behalf_of'));

        $this->checkCertificate();
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
                scope: 'InvoicingAPI',
                onbehalfof: $this->getOnBehalfOf(),
            );

            $accessToken = data_get($myinvois, 'data.access_token');

            if (!$accessToken) {
                throw new MyInvoisApiError($result ?? ['code' => __('Missing an access token.')]);
            }

            return $accessToken;
        }
    }

    public function generateDocument(Invoice $data, Format $format): string
    {
        return match ($format) {
            Format::XML => $this->generateXMLDocument(data: $data),
            Format::JSON => $this->generateJSONDocument(data: $data),
        };
    }

    public function generateXMLDocument(Invoice $data): string
    {
        $helper = new MyInvoisHelper();
        $service = $helper->createInvoiceXMLService();

        if ($this->hasSignature === true) {

            // add signature to document
            $sig = new MyInvoisSignature(
                document: $data,
                certificate: $this->getCertificate(),
            );

            $data->set('UBLExtensions', $sig->getUBLExtensions())
                ->set('Signature', $sig->getSignature());
        } else {
            // set to version 1.0 if no signature
            $data->InvoiceTypeCode?->attributes(['listVersionID' => '1.0']);
        }

        $content = $helper->writeXml($service, 'Invoice', $data->toXmlArray());

        $dom = $this->helper()->createDOM();
        $dom->loadXML($content);
        $content = $dom->C14N();

        // $helper->displayXml($content);

        return $content;
    }

    // in progress
    public function generateJSONDocument(Invoice $data)
    {

    }

    public function hasSignature($hasSignature = true): static
    {
        $this->hasSignature = $hasSignature;

        return $this;
    }

    public function getClientId(): string
    {
        return $this->client_id;
    }

    public function setClientId(string $clientId): void
    {
        $this->client_id = $clientId;
    }

    public function getClientSecret(): string
    {
        return $this->client_secret;
    }

    public function setClientSecret(string $clientSecret): void
    {
        $this->client_secret = $clientSecret;
    }

    public function getOnBehalfOf(): ?string
    {
        return $this->on_behalf_of;
    }

    public function setOnBehalfOf(?string $onBehalfOf): void
    {
        $this->on_behalf_of = $onBehalfOf;
    }

    public function isSandbox(): bool
    {
        return $this->is_sandbox;
    }

    public function setIsSandbox(bool $isSandBox): void
    {
        $this->is_sandbox = $isSandBox;
    }

    public function getCertificatePath(): ?string
    {
        return $this->certificate_path;
    }

    private function setCertificatePath(?string $certificate_path)
    {
        $this->certificate_path = $certificate_path;
    }

    public function getPrivateKeyPath(): ?string
    {
        return $this->private_key_path;
    }

    private function setPrivateKeyPath(?string $private_key_path)
    {
        $this->private_key_path = $private_key_path;
    }

    public function getPassphrase(): ?string
    {
        return $this->passphrase;
    }

    public function setPassphrase(?string $passphrase): void
    {
        $this->passphrase = $passphrase;
    }

    public function getHashAlgorithm(): string
    {
        return $this->hashAlgorithm;
    }

    public function getDocumentPath(): string
    {
        return $this->document_path;
    }

    public function setDocumentPath(?string $documentPath): void
    {
        $this->document_path = $documentPath;
    }

    public function getDisk(): string
    {
        return $this->disk;
    }

    public function setDisk(string $disk): void
    {
        $this->disk = $disk;
    }

    public function config(string $name): array|string|int|bool|null
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

    public function getCertificate(): ?MyInvoisCertificate
    {
        return $this->certificate;
    }

    private function setCertificate(): void
    {
        $certContent = file_get_contents($this->getCertificatePath());
        $privateKeyContent = null;
        $ext = pathinfo($this->getCertificatePath(), PATHINFO_EXTENSION);

        if ($ext === 'p12' || $ext === 'pfx') {
            if (!openssl_pkcs12_read($certContent, $certs, $this->getPassphrase())) {
                throw new MyInvoisException('OpenSSL Error: ' . openssl_error_string() ?? 'Invalid cetificate');
            }

            $certContent = data_get($certs, 'cert');
            $privateKeyContent = data_get($certs, 'pkey');
        } else {
            $privateKeyContent = file_get_contents($this->getPrivateKeyPath());
        }

        $certInfo = openssl_x509_parse($certContent);

        $this->certificate = new MyInvoisCertificate(
            certificate: $certContent,
            privateKey: $privateKeyContent,
            info: $certInfo
        );
    }

    private function checkCertificate()
    {
        if ($this->getCertificatePath() && !$this->helper()->isAbsolutePath($this->getCertificatePath())) {
            $this->setCertificatePath(base_path($this->getCertificatePath()));
        }

        if ($this->getPrivateKeyPath() && !$this->helper()->isAbsolutePath($this->getPrivateKeyPath())) {
            $this->setPrivateKeyPath(base_path($this->getPrivateKeyPath()));
        }

        if (
            $this->isFileExists($this->getCertificatePath())
            && $this->isFileExists($this->getPrivateKeyPath())
        ) {
            $this->setCertificate();
            $this->hasSignature = true;
        }
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

            if ($onbehalfof = data_get($arguments, 'onbehalfof')) {
                $this->setOnBehalfOf($onbehalfof);
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
}
