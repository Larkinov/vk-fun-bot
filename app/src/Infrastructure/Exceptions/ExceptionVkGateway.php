<?php

namespace App\Infrastructure\Exception;

use Exception;

class ExceptionVkGateway extends Exception
{

    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
