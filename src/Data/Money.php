<?php

namespace Laraditz\MyInvois\Data;

class Money
{
    public function __construct(
        public string $value,
        public string $currencyID,
    ) {
    }
}
