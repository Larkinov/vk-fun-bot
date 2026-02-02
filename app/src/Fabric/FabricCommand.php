<?php

namespace App\Fabric;

use App\Exception\ExceptionFabricNotFound;
use App\ValueObject\Command\AbstractCommand;
use App\ValueObject\VK\MessageVK;
use Psr\Log\LoggerInterface;

class FabricCommand
{
    public function __construct(private LoggerInterface $logger) {}

    public function getInstance(MessageVK $message): AbstractCommand
    {

        $command = ucfirst(substr($message->getText(), 1));

        $command = "App\\ValueObject\\Command\\$command"."Command";

        if (class_exists($command))
            return new $command($this->logger, $message);

        throw new ExceptionFabricNotFound('command', $command);
    }
}
