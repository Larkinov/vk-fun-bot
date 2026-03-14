<?php

namespace App\Application\UseCase;

use App\Application\Exceptions\ExceptionFactoryNotFound;
use App\Application\Factory\FactoryCommand;
use App\Domain\Gateway\DataGatewayInterface;
use App\Domain\ValueObject\VK\MessageVK;
use App\Infrastructure\Exceptions\ExceptionAccessGateway;
use Psr\Log\LoggerInterface;
use Throwable;

class ExecuteCommandUseCase
{

    private const MESSAGE_FAILED_COMMAND = '❗ Обнаружена ошибка! ID беседы ';
    private const SERVICE_MESSAGE_NOT_FOUND_COMMAND = '⚠🔎 Такая команда не найдена..';
    private const SERVICE_MESSAGE_FAILED_COMMAND = '⚠🔧 Что-то пошло не так, мы уже работаем над этой проблемой..';
    private const SERVICE_MESSAGE_FAILED_ACCESS = '⚠🤖 Боту не хватает прав для выполнения команды - необходимо выдать права администратора боту';

    public function __construct(
        private LoggerInterface $logger,
        private DataGatewayInterface $dataGateway,
        private FactoryCommand $factoryCommand,
    ) {}

    public function __invoke(MessageVK $messageVk): void
    {
        try {
            $command = $this->factoryCommand->getInstance($messageVk);

            $command->run();
        } catch (ExceptionFactoryNotFound $th) {
            $this->handleError($th, $messageVk->getPeerId(), self::SERVICE_MESSAGE_NOT_FOUND_COMMAND, false);
        } catch (ExceptionAccessGateway $th) {
            $this->handleError($th, $messageVk->getPeerId(), self::SERVICE_MESSAGE_FAILED_ACCESS);
        } catch (\Throwable $th) {
            $this->handleError($th, $messageVk->getPeerId(), self::SERVICE_MESSAGE_FAILED_COMMAND);
        }
    }

    private function handleError(Throwable $th, int $peerId, string $serviceMessage, bool $sendFailedMessage = true): void
    {
        $this->logger->error('failed command', ['message' => $th->getMessage(), 'file' => $th->getFile(), 'line' => $th->getLine(), 'trace' => $th->getTrace()]);
        $this->dataGateway->sendMessage($serviceMessage, $peerId);
        if (isset($_ENV['USER_SERVICE_ID']) && $sendFailedMessage)
            $this->dataGateway->sendMessage(self::MESSAGE_FAILED_COMMAND . $peerId . ":\n\n" .  $th->getMessage().';'.$th->getFile().';'.$th->getLine(), $_ENV['USER_SERVICE_ID']);
    }
}
