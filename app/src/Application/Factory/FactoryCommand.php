<?php

namespace App\Application\Factory;

use App\Application\Exceptions\ExceptionFactoryNotFound;
use App\Application\UseCase\SaveConversationUseCase;
use App\Application\UseCase\SaveProfileUseCase;
use App\Domain\Gateway\DataGatewayInterface;
use App\Domain\ValueObject\Command\AbstractCommand;
use App\Domain\ValueObject\VK\MessageVK;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FactoryCommand
{
    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private SaveConversationUseCase $saveConversationUseCase,
        private SaveProfileUseCase $saveProfileUseCase,
        private DataGatewayInterface $dataGateway,
        private TranslatorInterface $translator,
    ) {}

    public function getInstance(MessageVK $message): AbstractCommand
    {

        $command = $message->getCommand();

        $classname = "App\\Domain\\ValueObject\\Command\\$command" . "Command";

        if (class_exists($classname))
            return new $classname(
                $this->logger,
                $this->entityManager,
                $this->saveConversationUseCase,
                $this->saveProfileUseCase,
                $this->dataGateway,
                $this->translator,
                $message
            );

        throw new ExceptionFactoryNotFound('command', $classname);
    }
}
