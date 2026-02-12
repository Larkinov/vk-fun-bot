<?php

namespace App\Application\UseCase;

use App\Application\Factory\FactoryCommand;
use App\Domain\ValueObject\VK\MessageVK;
use Generator\Skeleton\skeleton\base\src\VK\CallbackApi\VKCallbackApiServerHandler;
use Psr\Log\LoggerInterface;

class ExecuteCommandUseCase extends VKCallbackApiServerHandler
{

    public function __construct(
        private LoggerInterface $logger,
        private FactoryCommand $factoryCommand,
    ) {}

    public function execute(MessageVK $messageVk): void
    {
        try {
            $command = $this->factoryCommand->getInstance($messageVk);

            $command->run();
        } catch (\Throwable $th) {
            $this->logger->error('failed create new command', ['message' => $th->getMessage(), 'trace' => $th->getTrace()]);
        }
    }
}
