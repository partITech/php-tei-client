<?php

namespace Partitech\PhpTeiClient;
use Exception;
use Throwable;
class TeiClientException extends Exception
{
    public function __construct(string $message, int $code, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}