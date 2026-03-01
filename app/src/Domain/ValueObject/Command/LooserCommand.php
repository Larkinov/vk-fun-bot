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
        $idLooser = $this->getRandomIdProfile();

        $this->updateLooserData($idLooser);
        $profile = $this->getProfile($idLooser);

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

    private function updateLooserData(int $idLooser): void
    {
        $looserData = $this->conversationDetails->getLooserData();

        $profiles = $looserData->getProfiles();

        $this->logger->info('find looser', ['id' => $idLooser, 'prev data' => $profiles, 'last active' => $looserData->getLastActive()]);

        $looserData = $looserData->incrementLooser($idLooser);

        $this->conversationDetails->setLooserData($looserData);
        $this->entityManager->persist($this->conversationDetails);
        $this->entityManager->flush();
    }
}
