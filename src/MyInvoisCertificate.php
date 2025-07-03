<?php

namespace Laraditz\MyInvois;

use Illuminate\Support\Collection;

class MyInvoisCertificate
{
    private ?string $issuerName = null;

    private ?string $rawCertificate = null;

    public function __construct(
        private ?string $certificate = null,
        private ?string $privateKey = null,
        private ?array $info = [],
    ) {
        $this->setIssuerName();
        $this->setRawCertificate();
    }

    private function setRawCertificate()
    {
        $hasBeginEnd = str($this->certificate)->contains(['Begin', 'END']) ? true : false;
        $collection = str($this->certificate)->replace("\r", "")->explode("\n");

        if ($collection->isNotEmpty()) {
            $collection = $collection
                ->filter();

            if ($hasBeginEnd) {
                $collection->pull(0);
                $collection->pop();
            }

            $this->rawCertificate = $collection->implode('');
        }
    }

    public function getCertificate(): ?string
    {
        return $this->certificate;
    }

    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    public function getIssuerName(): ?string
    {
        return $this->issuerName;
    }

    public function getRawCertificate(): ?string
    {
        return $this->rawCertificate;
    }

    private function setIssuerName()
    {
        $issuerArr = $this->getInfo('issuer');

        if ($issuerArr && is_array($issuerArr) && count($issuerArr) > 0) {

            $collection = $this->issuerNameSequence()
                ->map(fn(?string $value, string $key) => data_get($issuerArr, $key))
                ->reject(function (?string $value) {
                    return empty($value);
                });

            if ($collection->isNotEmpty()) {
                $this->issuerName = $collection->implode(function (string $value, string $key) {
                    return $key . '=' . $value;
                }, ', ');
            }
        }
    }

    // sequence follow sample at https://sdk.myinvois.hasil.gov.my/files/sdksamples/1.1-Invoice-Sample.xml
    private function issuerNameSequence(): Collection
    {
        return collect([
            'CN' => null,
            'E' => null,
            'OU' => null,
            'O' => null,
            'C' => null,
        ]);
    }

    public function getInfo(string $name): array|string|null
    {
        return data_get($this->info, $name);
    }

}