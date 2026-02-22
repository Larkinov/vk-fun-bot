<?php

namespace App\Domain\Exceptions;

use Exception;

class ExceptionNullMemberId extends Exception
{

    private const MESSAGE = 'member id is null';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
