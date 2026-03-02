<?php

namespace App\Domain\Exceptions;

use Exception;

class ExceptionUnknownDelayMode extends Exception
{

    private const MESSAGE = 'unknown delay mode - ';

    public function __construct(string $mode)
    {
        parent::__construct(self::MESSAGE . $mode);
    }
}
