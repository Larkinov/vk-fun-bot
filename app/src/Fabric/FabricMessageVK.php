<?php

namespace App\Fabric;

use App\ValueObject\VK\MessageVK;
use Psr\Log\LoggerInterface;

class FabricMessageVK
{
    public function __construct(private LoggerInterface $logger) {}

    public function getInstance(array $object): MessageVK
    {
        return new MessageVK($this->logger, $object);
    }
}
