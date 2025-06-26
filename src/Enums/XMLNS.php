<?php

namespace Laraditz\MyInvois\Enums;

enum XMLNS: string
{
    case CAC = 'cac';
    case CBC = 'cbc';

    public function getNamespace()
    {
        return match ($this) {
            static::CAC => 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            static::CBC => 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            default => ''
        };
    }

    public static function __callStatic($method, $arguments)
    {
        try {
            return static::{$method}?->value;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
