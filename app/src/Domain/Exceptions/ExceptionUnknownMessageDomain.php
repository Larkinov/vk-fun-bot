<?php

namespace App\Domain\Exceptions;

use Exception;

class ExceptionUnknownMessageDomain extends Exception
{

    public function __construct(string $domain)
    {
        parent::__construct("unknown message domain - $domain");
    }
}
