<?php

namespace App\Application\Factory;

use App\Application\Exceptions\ExceptionFactoryNotFound;
use App\Application\UseCase\SaveConversationUseCase;
use App\Domain\ValueObject\Command\AbstractCommand;
use App\Domain\ValueObject\VK\MessageVK;
use App\Infrastructure\Gateway\VkGateway;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class FactoryCommand
{
    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        protected SaveConversationUseCase $saveConversationUseCase,
        private VkGateway $vkGateway,
        private FactoryConversation $factoryConversation,
    ) {}

    public function getInstance(MessageVK $message): AbstractCommand
    {

        $command = ucfirst(substr($message->getText(), 1));

        $command = "App\\Domain\\ValueObject\\Command\\$command" . "Command";

        if (class_exists($command))
            return new $command($this->logger, $this->entityManager, $this->saveConversationUseCase, $this->vkGateway, $this->factoryConversation, $message);

        throw new ExceptionFactoryNotFound('command', $command);
    }
}
