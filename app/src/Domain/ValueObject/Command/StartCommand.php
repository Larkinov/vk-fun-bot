<?php

namespace App\Domain\ValueObject\Command;

class StartCommand extends AbstractCommand
{

    public function run(): void
    {
        $userName = $this->messageGateway->getUser($this->getFromId());
        // $this->vkGateway->sendMessage('lalalalala - ' . $userName, $this->fromId);

        $conversation = ($this->saveConversationUseCase)($this->peerId);
    }
}
