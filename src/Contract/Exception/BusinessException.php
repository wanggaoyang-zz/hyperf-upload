<?php


namespace Wgy\Upload\Contract\Exception;

use Exception;
use Throwable;

class BusinessException extends Exception
{
    public function __construct(string $message = '', int $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
