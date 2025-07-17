<?php

namespace Laraditz\MyInvois\Enums;

use Laraditz\MyInvois\Exceptions\MyInvoisException;

enum DocumentStatus: int
{
    case Submitted = 1;
    case Valid = 2;
    case Invalid = 3;
    case Cancelled = 4;

    public static function fromName(string $name)
    {
        try {
            return self::{$name};
        } catch (\Throwable $th) {
            throw new MyInvoisException("$name is not a valid backing value for enum " . self::class);
        }
    }

    public static function tryFromName(string $name)
    {
        try {
            return self::fromName($name);
        } catch (\Throwable $th) {
            return null;
        }
    }
}
