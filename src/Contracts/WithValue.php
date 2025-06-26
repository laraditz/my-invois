<?php

namespace Laraditz\MyInvois\Contracts;

use Laraditz\MyInvois\Enums\XMLNS;

interface WithValue
{
    public function getValue(string $name): mixed;
}