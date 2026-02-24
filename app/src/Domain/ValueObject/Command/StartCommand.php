<?php

namespace App\Domain\ValueObject\Command;

class StartCommand extends AbstractCommand
{

    public function run(): void
    {
        if ($this->isDisabledConversation()) {
            $this->logger->info('init command', $this->context);
            ($this->saveConversationUseCase)($this->peerId);

            return;
        }

        $this->logger->info('active profiles', $this->conversation->getActiveProfileIds() + ['active member' => $this->conversation->getActiveMemberCount(), 'member' => $this->conversation->getMemberCount(),'activedAt'=>$this->conversation->getDetails()->getActivatedAt()]);
    }
}
