<?php

namespace Laraditz\MyInvois\Enums;

enum State: string
{
    case Johor = '01';
    case Kedah = '02';
    case Kelantan = '03';
    case Melaka = '04';
    case NegeriSembilan = '05';
    case Pahang = '06';
    case PulauPinang = '07';
    case Perak = '08';
    case Perlis = '09';
    case Selangor = '10';
    case Terengganu = '11';
    case Sabah = '12';
    case Sarawak = '13';
    case WPKualaLumpur = '14';
    case WPLabuan = '15';
    case WPPutrajaya = '16';
    case NotApplicable = '17';

    public function getDescription(): ?string
    {
        return match ($this) {
            static::WPKualaLumpur => 'Wilayah Persekutuan Kuala Lumpur',
            static::WPLabuan => 'Wilayah Persekutuan Labuan',
            static::WPPutrajaya => 'Wilayah Persekutuan Putrajaya',
            default => str($this->name)->headline()->value,
        };
    }
}
