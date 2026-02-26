<?php

namespace App\Domain\ValueObject\Command;

use App\Domain\Exceptions\ExceptionNullMemberId;

class KickUserCommand extends AbstractCommand
{

    public function run(): void
    {
        if ($this->isNewConversation())
            return;


        $this->logger->info('init command', $this->context);

        if (is_null($this->memberId))
            throw new ExceptionNullMemberId;

        $inactiveProfiles = $this->conversation->getInactiveProfileIds();

        if (!in_array($this->memberId, $inactiveProfiles)) {
            $this->logger->info('add profile (member_id) in inactives');
            $inactiveProfiles[] = $this->memberId;
            $this->conversation->setInactiveProfileIds($inactiveProfiles);

            $this->entityManager->persist($this->conversation);
            $this->entityManager->flush();
        }
    }
}
