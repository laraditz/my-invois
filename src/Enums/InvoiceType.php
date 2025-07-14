<?php

namespace Laraditz\MyInvois\Enums;

enum InvoiceType: string
{
    case Invoice = '01';
    case CreditNote = '02';
    case DebitNote = '03';
    case RefundNote = '04';
    case SelfBilledInvoice = '11';
    case SelfBilledCreditNote = '12';
    case SelfBilledDebitNote = '13';
    case SelfBilledRefundNote = '14';

    public static function __callStatic($method, $arguments)
    {
        try {
            return static::{$method}?->value;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
