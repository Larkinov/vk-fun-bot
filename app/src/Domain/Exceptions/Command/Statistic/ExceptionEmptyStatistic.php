<?php

namespace App\Domain\Exceptions\Command\Statistic;

use Exception;

class ExceptionEmptyStatistic extends Exception
{

    private const MESSAGE = 'empty statistic data from table conversation details';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
