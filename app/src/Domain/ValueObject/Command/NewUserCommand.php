<?php

namespace App\Domain\ValueObject\Command;

class NewUserCommand extends AbstractCommand
{

    public function run(): void
    {
        $this->logger->info('new user');
    }
}
