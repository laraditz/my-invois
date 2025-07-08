<?php

namespace Laraditz\MyInvois\Contracts;

use Laraditz\MyInvois\Enums\XMLNS;

interface WithAttributes
{
    public function getAttributes(): array;
}