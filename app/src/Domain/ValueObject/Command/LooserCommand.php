<?php

namespace App\Domain\ValueObject\Command;

use App\Domain\Builder\MessageBuilder;

class LooserCommand extends AbstractCommand
{

    public function run(): void
    {
        if ($this->isNewConversation())
            return;

        $this->dataGateway->sendMessage($this->getMessage(), $this->peerId);
    }

    protected function getMessage(array $options = []): string
    {
        $profile = $this->getProfile($this->getRandomIdProfile());

        return $this->messageBuilder
            ->setMessageId('command.looser')
            ->setProfile($profile)
            ->setDomain(MessageBuilder::DOMAIN_LOOSER)
            ->build();
    }

    private function getRandomIdProfile(): int
    {
        $profiles = $this->conversation->getActiveProfileIds();
        return $profiles[random_int(0, count($profiles) - 1)];
    }
}
