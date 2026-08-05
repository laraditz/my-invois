<?php

namespace Laraditz\MyInvois\Tests\Concerns;

use Laraditz\MyInvois\MyInvoisCertificate;

trait GeneratesTestCertificate
{
    protected function generateTestCertificate(): MyInvoisCertificate
    {
        // PHP's openssl extension needs an explicit config file on some
        // platforms (notably Windows) to locate its default settings -
        // without it, openssl_pkey_new()/openssl_csr_new() fail outright.
        // A minimal, self-contained config keeps this portable across
        // environments instead of depending on a system-wide openssl.cnf.
        $config = ['config' => __DIR__ . '/../fixtures/openssl.cnf'];

        $privateKey = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ...$config,
        ]);

        $csr = openssl_csr_new([
            'commonName' => 'MyInvois Test Certificate',
            'countryName' => 'MY',
        ], $privateKey, $config);

        $x509 = openssl_csr_sign($csr, null, $privateKey, 365, $config, random_int(1, PHP_INT_MAX));

        openssl_x509_export($x509, $certificatePem);
        openssl_pkey_export($privateKey, $privateKeyPem, null, $config);

        $info = openssl_x509_parse($certificatePem);

        return new MyInvoisCertificate(
            certificate: $certificatePem,
            privateKey: $privateKeyPem,
            info: $info,
        );
    }
}
