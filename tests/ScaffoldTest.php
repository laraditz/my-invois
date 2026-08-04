<?php

namespace Laraditz\MyInvois\Tests;

use Laraditz\MyInvois\MyInvoisServiceProvider;

class ScaffoldTest extends TestCase
{
    public function test_the_application_boots_and_registers_the_service_provider()
    {
        $this->assertTrue(class_exists(MyInvoisServiceProvider::class));
        $this->assertTrue($this->app->providerIsLoaded(MyInvoisServiceProvider::class));
    }
}
