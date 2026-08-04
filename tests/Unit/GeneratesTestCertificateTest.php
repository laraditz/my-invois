<?php

namespace Laraditz\MyInvois\Tests\Unit;

use Laraditz\MyInvois\Tests\Concerns\GeneratesTestCertificate;
use Laraditz\MyInvois\Tests\TestCase;

class GeneratesTestCertificateTest extends TestCase
{
    use GeneratesTestCertificate;

    public function test_it_generates_a_usable_self_signed_certificate()
    {
        $certificate = $this->generateTestCertificate();

        $this->assertNotEmpty($certificate->getRawCertificate());
        $this->assertNotEmpty($certificate->getPrivateKey());
        $this->assertNotEmpty($certificate->getIssuerName());
    }
}
