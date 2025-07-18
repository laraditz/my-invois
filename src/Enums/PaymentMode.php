<?php

namespace Laraditz\MyInvois\Enums;

enum PaymentMode: string
{
    case Cash = '01';
    case Cheque = '02';
    case BankTransfer = '03';
    case CreditCard = '04';
    case DebitCard = '05';
    case EWallet = '06';
    case DigitalBank = '07';
    case Others = '08';

    public function getDescription(): ?string
    {
        return match ($this) {
            static::EWallet => 'e-Wallet / Digital Wallet',
            default => str($this->name)->headline()->value,
        };
    }
}
