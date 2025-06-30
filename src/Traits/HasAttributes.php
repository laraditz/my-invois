<?php

namespace Laraditz\MyInvois\Traits;

use Laraditz\MyInvois\Enums\XMLNS;

trait HasAttributes
{
    public array $attributes = [];

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}