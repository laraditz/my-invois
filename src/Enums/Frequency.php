<?php

namespace Laraditz\MyInvois\Enums;

enum Frequency: string
{
    case Daily = 'Daily';
    case Weekly = 'Weekly';
    case Biweekly = 'Biweekly';
    case Monthly = 'Monthly';
    case Bimonthly = 'Bimonthly';
    case Quarterly = 'Quarterly';
    case HalfYearly = 'Half-yearly';
    case Yearly = 'Yearly';
    case NotApplicable = 'Others / Not Applicable';

    public static function __callStatic($method, $arguments)
    {
        try {
            return static::{$method}?->value;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
