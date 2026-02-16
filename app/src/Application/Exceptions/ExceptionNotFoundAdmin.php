<?php

namespace App\Application\Exceptions;

use Exception;

class ExceptionNotFoundAdmin extends Exception
{
    private const MESSAGE = "Not found admin in conversation";

    public function __construct(int $peerId)
    {
        parent::__construct(self::MESSAGE . "; peer_id - $peerId");
    }
}
