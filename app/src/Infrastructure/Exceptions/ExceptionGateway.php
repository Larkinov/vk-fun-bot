<?php
declare(strict_types=1);
namespace App\Infrastructure\Exceptions;

use Exception;

class ExceptionGateway extends Exception
{

    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
