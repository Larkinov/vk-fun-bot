<?php

namespace App\Application\Factory;

use App\Application\Exceptions\ExceptionFactoryNotFound;
use App\Application\UseCase\SaveConversationUseCase;
use App\Domain\Gateway\MessageGatewayInterface;
use App\Domain\ValueObject\Command\AbstractCommand;
use App\Domain\ValueObject\VK\MessageVK;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class FactoryCommand
{
    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        protected SaveConversationUseCase $saveConversationUseCase,
        private MessageGatewayInterface $messageGateway,
    ) {}

    public function getInstance(MessageVK $message): AbstractCommand
    {

        $command = ucfirst(substr($message->getText(), 1));

        $command = "App\\Domain\\ValueObject\\Command\\$command" . "Command";

        if (class_exists($command))
            return new $command($this->logger, $this->entityManager, $this->saveConversationUseCase, $this->messageGateway, $message);

        throw new ExceptionFactoryNotFound('command', $command);
    }
}
