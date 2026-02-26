<?php

namespace App\Domain\ValueObject\Command;

class StartCommand extends AbstractCommand
{

    public function run(): void
    {
        if ($this->isNewConversation()) {
            $this->logger->info('init command', $this->context);
            ($this->saveConversationUseCase)($this->peerId);

            return;
        }
    }
}
