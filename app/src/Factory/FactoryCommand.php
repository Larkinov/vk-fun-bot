<?php

namespace App\Factory;

use App\Exception\Factory\ExceptionFactoryNotFound;
use App\Gateway\VkGateway;
use App\ValueObject\Command\AbstractCommand;
use App\ValueObject\VK\MessageVK;
use Psr\Log\LoggerInterface;

class FactoryCommand
{
    public function __construct(private LoggerInterface $logger, private VkGateway $vkGateway) {}

    public function getInstance(MessageVK $message): AbstractCommand
    {

        $command = ucfirst(substr($message->getText(), 1));

        $command = "App\\ValueObject\\Command\\$command" . "Command";

        if (class_exists($command))
            return new $command($this->logger, $this->vkGateway, $message);

        throw new ExceptionFactoryNotFound('command', $command);
    }
}
