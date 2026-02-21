<?php

namespace App\Infrastructure\Requests;

use App\Application\Factory\FactoryMessageVK;
use App\Application\UseCase\ExecuteCommandUseCase;
use App\Domain\Exceptions\ExceptionNotValidCommand;
use Generator\Skeleton\skeleton\base\src\VK\CallbackApi\VKCallbackApiServerHandler;
use Psr\Log\LoggerInterface;

class VkRequest extends VKCallbackApiServerHandler
{

    public function __construct(
        private LoggerInterface $logger,
        private ExecuteCommandUseCase $executeCommandUseCase,
        private FactoryMessageVK $factoryMessageVk,
    ) {}

    public function messageNew(int $group_id, ?string $secret, array $object): void
    {
        $this->logger->info('message new', ['id' => $group_id, 'secret' => $secret, 'object' => $object]);

        try {
            $messageVk = $this->factoryMessageVk->getInstance($object);
            ($this->executeCommandUseCase)($messageVk);
        } catch (ExceptionNotValidCommand $th) {
        }
    }
}
