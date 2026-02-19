<?php

namespace App\Domain\ValueObject\Command;

class StartCommand extends AbstractCommand
{

    public function run(): void
    {
        $userName = $this->dataGateway->getUser($this->getFromId());

        $conversation = ($this->saveConversationUseCase)($this->peerId);

        $this->logger->info('conversation profile',['profile'=>$conversation->getProfileIds()]);
        // $conversation = ($this->saveProfilenUseCase)($this->peerId);
    }
}
