<?php

namespace Laraditz\MyInvois\Exceptions;

use Exception;
use Throwable;

class MyInvoisAPIError extends Exception
{
    protected array $result = [];

    protected ?string $correlationId = null;

    protected ?string $messageCode = null;

    public function __construct(
        array $result = [],
        string $message = 'API Error',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->result = $result;
        $this->correlationId = data_get($this->result, 'correlationId');
        $this->messageCode = data_get($this->result, 'code');
        $message = ($this->messageCode ? $this->messageCode . ': ' : '') . data_get($this->result, 'message') ?? $message;

        parent::__construct($message, $code, $previous);
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getMessageCode()
    {
        return $this->messageCode;
    }


    public function getCorrelationId()
    {
        return $this->correlationId;
    }
}
