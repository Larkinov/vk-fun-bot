<?php

namespace App\Domain\ValueObject\Command;

use App\Application\Dto\CreateProfileDto;
use App\Domain\Exceptions\ExceptionNullMemberId;

class NewUserCommand extends AbstractCommand
{

    public function run(): void
    {
        if ($this->isDisabledConversation())
            return;
        


        $this->logger->info('init command', $this->context);

        if (is_null($this->memberId))
            throw new ExceptionNullMemberId;

        $profiles = $this->conversation->getProfileIds();

        if (!in_array($this->memberId, $profiles)) {
            $this->logger->info('add new profile (member_id) in conversation', $this->context);
            $profiles[] = $this->memberId;
            $this->conversation->setProfileIds($profiles);
            $this->conversation->setMemberCount(count($profiles));

            $user = $this->dataGateway->getUser($this->memberId);

            $this->logger->info('get new user from VK', $this->context + ['user' => $user]);

            ($this->saveProfileUseCase)($this->conversation,new CreateProfileDto($this->peerId, $user));
        }

        $inactiveProfiles = $this->conversation->getInactiveProfileIds();

        if (in_array($this->memberId, $inactiveProfiles)) {
            $newInactiveIds = array_diff($inactiveProfiles, [$this->memberId]);
            $this->logger->info('delete profile from inactives', $this->context + ['new inactive profiles' => $newInactiveIds]);
            $this->conversation->setInactiveProfileIds($newInactiveIds);
            $this->entityManager->persist($this->conversation);
            $this->entityManager->flush();
        }
    }
}
