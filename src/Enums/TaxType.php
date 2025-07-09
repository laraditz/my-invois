<?php

namespace Laraditz\MyInvois\Enums;

enum TaxType: string
{
    case Sales = '01';
    case Service = '02';
    case Tourism = '03';
    case HighValueGoods = '04';
    case SalesTaxOnLowValueGoods = '05';
    case NotApplicable = '06';
    case Exemption = 'E';

    public function getDescription(): ?string
    {
        return match ($this) {
            static::Sales, static::Service, static::Tourism => $this->name . ' Tax',
            static::HighValueGoods => 'High-Value Goods Tax',
            static::Exemption => 'Tax exemption (where applicable)',
            default => str($this->name)->headline()->apa()->value,
        };
    }
}
