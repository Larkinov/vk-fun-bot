<?php

namespace App\Domain\Exceptions;

use Exception;

class ExceptionNotValidCommand extends Exception
{

    private const MESSAGE = 'is not valid command';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
