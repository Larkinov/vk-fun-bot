<?php

namespace App\Application\UseCase;

use App\Application\Factory\FactoryCommand;
use App\Domain\Gateway\MessageGatewayInterface;
use App\Domain\ValueObject\VK\MessageVK;
use Psr\Log\LoggerInterface;

class ExecuteCommandUseCase
{

    private const MESSAGE_FAILED_COMMAND = '❗ Обнаружена ошибка! ID беседы ';
    private const SERVICE_MESSAGE_FAILED_COMMAND = '🌧 Что-то пошло не так, мы уже работаем над этой проблемой..';

    public function __construct(
        private LoggerInterface $logger,
        private MessageGatewayInterface $messageGateway,
        private FactoryCommand $factoryCommand,
    ) {}

    public function __invoke(MessageVK $messageVk): void
    {
        try {
            $command = $this->factoryCommand->getInstance($messageVk);

            $command->run();
        } catch (\Throwable $th) {
            $this->logger->error('failed create new command', ['message' => $th->getMessage(), 'trace' => $th->getTrace()]);
            $this->messageGateway->sendMessage(self::SERVICE_MESSAGE_FAILED_COMMAND, $messageVk->getPeerId());
            if (isset($_ENV['USER_SERVICE_ID']))
                $this->messageGateway->sendMessage(self::MESSAGE_FAILED_COMMAND . $messageVk->getPeerId() . ":\n\n" .  $th->getMessage(), $_ENV['USER_SERVICE_ID']);
        }
    }
}
