<?php

namespace Laraditz\MyInvois\Exceptions;

use Exception;
use Throwable;

class MyInvoisException extends Exception
{
    public function __construct(
        string $message = 'MyInvois Exception.',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
