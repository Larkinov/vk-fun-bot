<?php

namespace App\Domain\ValueObject\Command;

use App\Domain\Builder\MessageBuilder;
use App\Domain\Services\TimeService;
use App\Domain\ValueObject\Command\Data\LooserData;

class LooserCommand extends AbstractCommand
{

    private const DELAY_TIME_HOURS = 8;
    public function run(): void
    {
        if ($this->isNewConversation())
            return;

        $this->dataGateway->sendMessage($this->getMessage(), $this->peerId);
    }

    protected function getMessage(array $options = []): string
    {
        $idLooser = $this->getRandomIdProfile();
        $looserData = $this->conversationDetails->getLooserData();

        $remainingTime = $this->timeService->getRemainingTime(
            $looserData->getLastActive(),
            TimeService::DELAY_MODE_HOUR,
            self::DELAY_TIME_HOURS
        );

        if ($remainingTime <= 0) {
            $this->updateLooserData($looserData, $idLooser);

            return $this->messageBuilder
                ->setMessageId('command.looser')
                ->setProfile($this->getProfile($idLooser))
                ->setDomain(MessageBuilder::DOMAIN_LOOSER)
                ->build();
        } else {
            return $this->messageBuilder
                ->setMessageId('service.denied.delay_time')
                ->addNewOptions('hour', $remainingTime)
                ->build();
        }
    }

    private function getRandomIdProfile(): int
    {
        $profiles = $this->conversation->getActiveProfileIds();
        return $profiles[random_int(0, count($profiles) - 1)];
    }

    private function updateLooserData(LooserData $looserData, int $idLooser): void
    {
        $profiles = $looserData->getProfiles();

        $this->logger->info('find looser', ['id' => $idLooser, 'prev data' => $profiles, 'last active' => $looserData->getLastActive()]);

        $looserData = $looserData->incrementLooser($idLooser);

        $this->conversationDetails->setLooserData($looserData);
        $this->entityManager->persist($this->conversationDetails);
        $this->entityManager->flush();
    }
}
