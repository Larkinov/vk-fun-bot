<?php

namespace App\Domain\ValueObject\Command;

class StartCommand extends AbstractCommand
{

    public function run(): void
    {
        if ($this->isNewConversation()) {
            $this->logger->info('first init command', $this->context);
            ($this->saveConversationUseCase)($this->peerId);

            $message = $this->translator->trans('command.start.first', ['botName' => $_ENV['BOT_NAME']], 'main');

            $this->dataGateway->sendMessage($message, $this->peerId);
            return;
        }

        $this->logger->info('repeat init command', $this->context);

        $message = $this->translator->trans('command.start.repeat', ['botName' => $_ENV['BOT_NAME']], 'main');

        $this->dataGateway->sendMessage($message, $this->peerId);
    }
}
