<?php

namespace App\Service;

use App\Factory\FactoryCommand;
use App\Factory\FactoryMessageVK;
use App\ValueObject\VK\CommandVK;
use App\ValueObject\VK\MessageVK;
use Generator\Skeleton\skeleton\base\src\VK\CallbackApi\VKCallbackApiServerHandler;
use Psr\Log\LoggerInterface;

class ServerVkHandler extends VKCallbackApiServerHandler
{

    public function __construct(
        private LoggerInterface $logger,
        private FactoryMessageVK $factoryMessageVk,
        private FactoryCommand $factoryCommand,
    ) {}

    public function messageNew(int $group_id, ?string $secret, array $object): void
    {
        $this->logger->info('message new', ['id' => $group_id, 'secret' => $secret, 'object' => $object]);

        try {
            $messageVk = $this->factoryMessageVk->getInstance($object);

            if (!MessageVK::isCorrectCommand($this->logger, $messageVk->getText()))
                return;

            $command = $this->factoryCommand->getInstance($messageVk);

            $command->run();
        } catch (\Throwable $th) {
            $this->logger->error('failed create new command', ['message' => $th->getMessage(), 'trace' => $th->getTrace()]);
        }
    }
}
