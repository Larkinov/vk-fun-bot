<?php

namespace App\Exception\Gateway;

use Exception;

class ExceptionVkGateway extends Exception
{

    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
