<?php

namespace Laraditz\MyInvois\Contracts;

use Laraditz\MyInvois\Enums\XMLNS;

interface WithNamespace
{
    public function ns(string $name): ?XMLNS;
}