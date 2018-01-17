<?php

namespace Rk\Service\Exception;


use Throwable;

class MissingParam extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $message = 'Missing parameter ' . $message;
        parent::__construct($message, $code, $previous);
    }
}