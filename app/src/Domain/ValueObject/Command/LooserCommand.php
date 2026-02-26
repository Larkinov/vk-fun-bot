<?php

namespace App\Domain\ValueObject\Command;

class LooserCommand extends AbstractCommand
{

    public function run(): void
    {
        if ($this->isNewConversation())
            return;

        $this->logger->info('looser', ['id' => $this->getRandomProfile()]);
    }

    private function getRandomProfile(): int
    {
        $profiles = $this->conversation->getActiveProfileIds();
        return $profiles[random_int(0, count($profiles) - 1)];
    }
}
