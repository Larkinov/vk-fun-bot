<?php

namespace App\Domain\ValueObject\Command;

class StartCommand extends AbstractCommand
{

    public function run(): void
    {
        $conversation = ($this->saveConversationUseCase)($this->peerId);

        $this->logger->info('conversation data',['profile'=>$conversation->getProfileIds(),$conversation->getAdminId()]);
        // $conversation = ($this->saveProfilenUseCase)($this->peerId);
    }
}
