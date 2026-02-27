<?php

namespace App\Domain\ValueObject\Command;

class HelpCommand extends AbstractCommand
{

    public function run(): void
    {
        if ($this->isNewConversation())
            return;

        $this->dataGateway->sendMessage($this->getMessage(), $this->peerId);
    }

    protected function getMessage(array $options = []): string
    {
        return $this->messageBuilder
            ->setMessageId('command.help')
            ->build();
    }
}
