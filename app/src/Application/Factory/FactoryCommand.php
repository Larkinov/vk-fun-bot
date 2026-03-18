<?php
declare(strict_types=1);
namespace App\Application\Factory;

use App\Application\Dto\MessageVK;
use App\Application\Exceptions\Factory\ExceptionFactoryNotFound;
use App\Application\UseCase\Command\AbstractCommand;
use App\Application\UseCase\SaveConversationUseCase;
use App\Application\UseCase\SaveProfileUseCase;
use App\Domain\Builder\MessageBuilder;
use App\Domain\Gateway\DataGatewayInterface;
use App\Domain\Services\TimeService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class FactoryCommand
{
    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private SaveConversationUseCase $saveConversationUseCase,
        private SaveProfileUseCase $saveProfileUseCase,
        private DataGatewayInterface $dataGateway,
        private MessageBuilder $messageBuilder,
        private TimeService $timeService,
    ) {}

    public function getInstance(MessageVK $message): AbstractCommand
    {

        $command = $message->getCommand();

        $classname = "App\\Application\\UseCase\\Command\\$command" . "Command";

        if (class_exists($classname))
            return new $classname(
                $this->logger,
                $this->entityManager,
                $this->saveConversationUseCase,
                $this->saveProfileUseCase,
                $this->dataGateway,
                $this->messageBuilder,
                $this->timeService,
                $message
            );

        throw new ExceptionFactoryNotFound('command', $classname);
    }
}
