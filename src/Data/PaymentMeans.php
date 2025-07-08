<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Enums\XMLNS;

class PaymentMeans extends AbstractData
{
    public function __construct(
        public string $PaymentMeansCode,
        public ?PayeeFinancialAccount $PayeeFinancialAccount = null,
    ) {
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'PaymentMeansCode' => XMLNS::CBC,
            'PayeeFinancialAccount' => XMLNS::CAC,
            default => null
        };
    }
}
