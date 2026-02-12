<?php

namespace App\Exception\Configuration;

use Exception;

class ExceptionNullParamConfiguration extends Exception
{
    private const MESSAGE = "nullable parameter";

    public function __construct(string $param)
    {
        parent::__construct(self::MESSAGE . " - $param");
    }
}
