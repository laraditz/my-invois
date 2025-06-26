<?php

namespace Laraditz\MyInvois\Data;

class Data
{
    public function __construct(
        public string $value,
        public ?array $attributes = [],
    ) {
    }
}
