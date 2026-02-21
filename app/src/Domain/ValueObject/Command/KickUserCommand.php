<?php

namespace App\Domain\ValueObject\Command;

class KickUserCommand extends AbstractCommand
{

    public function run(): void
    {
        $this->logger->info('kick user');
    }
}
