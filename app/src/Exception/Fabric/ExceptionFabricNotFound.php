<?php

namespace App\Exception;

use Exception;

class ExceptionFabricNotFound extends Exception
{
    private const MESSAGE = "Not found create object";

    public function __construct(string $nameCreateObject, string $receivedName)
    {
        parent::__construct(self::MESSAGE . " - $nameCreateObject; received name - $receivedName");
    }
}
