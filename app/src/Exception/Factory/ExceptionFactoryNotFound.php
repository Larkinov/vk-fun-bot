<?php

namespace App\Exception\Factory;

use Exception;

class ExceptionFactoryNotFound extends Exception
{
    private const MESSAGE = "Not found create object";

    public function __construct(string $nameCreateObject, string $receivedName)
    {
        parent::__construct(self::MESSAGE . " - $nameCreateObject; received name - $receivedName");
    }
}
