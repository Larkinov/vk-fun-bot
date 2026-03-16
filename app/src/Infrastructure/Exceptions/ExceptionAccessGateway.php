<?php
declare(strict_types=1);
namespace App\Infrastructure\Exceptions;

use Exception;

class ExceptionAccessGateway extends Exception
{

    private const MESSAGE = 'dont have access';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
