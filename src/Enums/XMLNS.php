<?php

namespace Laraditz\MyInvois\Enums;

enum XMLNS: string
{
    case CAC = 'cac';
    case CBC = 'cbc';
    case EXT = 'ext';
    case SIG = 'sig';
    case SAC = 'sac';
    case SBC = 'sbc';
    case DS = 'ds';
    case XADES = 'xades';
    case NONE = '';

    public function getNamespace(): string
    {
        return match ($this) {
            static::CAC => 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            static::CBC => 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            static::EXT => 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2',
            static::SIG => 'urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2',
            static::SAC => 'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2',
            static::SBC => 'urn:oasis:names:specification:ubl:schema:xsd:SignatureBasicComponents-2',
            static::DS => 'http://www.w3.org/2000/09/xmldsig#',
            static::XADES => 'http://uri.etsi.org/01903/v1.3.2#',
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
