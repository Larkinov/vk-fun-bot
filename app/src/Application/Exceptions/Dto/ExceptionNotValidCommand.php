<?php
declare(strict_types=1);
namespace App\Application\Exceptions\Dto;

use Exception;

class ExceptionNotValidCommand extends Exception
{

    private const MESSAGE = 'is not valid command';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
