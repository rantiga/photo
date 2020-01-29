<?php

namespace app\httpSenders;

use Throwable;

class ExceptionResponse extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        http_response_code($code);
        header('Content-Type: application/json');

        echo json_encode([$message]);
        exit;
    }
}