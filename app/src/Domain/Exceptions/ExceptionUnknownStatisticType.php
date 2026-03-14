<?php

namespace App\Domain\Exceptions;

use Exception;

class ExceptionUnknownStatisticType extends Exception
{

    private const MESSAGE = 'unknown statistic type - ';

    public function __construct($type)
    {
        parent::__construct(self::MESSAGE . $type);
    }
}
