<?php

namespace App\Factory;

use App\ValueObject\VK\MessageVK;
use Psr\Log\LoggerInterface;

class FactoryMessageVK
{
    public function __construct(private LoggerInterface $logger) {}

    public function getInstance(array $object): MessageVK
    {
        return new MessageVK($this->logger, $object);
    }
}
