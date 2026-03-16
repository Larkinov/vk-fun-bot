<?php
declare(strict_types=1);
namespace App\Domain\ValueObject\Command;

use App\Domain\Builder\MessageBuilder;
use App\Domain\Services\TimeService;
use App\Domain\ValueObject\Command\Data\LooserData;

class LooserCommand extends AbstractCommand
{

    private const DELAY_TIME_HOURS = 8;
    private const LAST_NUMBER_TEXT = 103;

    private int $remainingTime;
    private bool $isNewWeek = false;
    private bool $isNewMonth = false;


    public function run(): void
    {
        if ($this->isNewConversation())
            return;

        $this->dataGateway->sendMessage($this->getMessage(), $this->peerId);

        if ($this->remainingTime <= 0) {
            $this->checkStatistic();
        }
    }

    protected function getMessage(array $options = []): string
    {
        $idLooser = $this->getRandomIdProfile();
        $looserData = $this->conversationDetails->getLooserData();

        $this->remainingTime = $this->timeService->getRemainingTime(
            $looserData->getLastActive(),
            TimeService::DELAY_MODE_HOUR,
            self::DELAY_TIME_HOURS
        );

        if ($this->remainingTime <= 0) {

            $this->isNewWeek = StatisticCommand::isNewWeek($looserData);
            $this->isNewMonth = StatisticCommand::isNewMonth($looserData);

            $this->updateLooserData($looserData, $idLooser);

            return $this->messageBuilder
                ->setMessageId($this->getRandomText())
                ->setProfile($this->getProfile($idLooser))
                ->setDomain(MessageBuilder::DOMAIN_LOOSER)
                ->build();
        } else {
            return $this->messageBuilder
                ->setMessageId('service.denied.delay_time')
                ->addNewOptions('hour', (string)$this->remainingTime)
                ->setDomain(MessageBuilder::DOMAIN_MAIN)
                ->build();
        }
    }

    private function getRandomIdProfile(): int
    {
        $profiles = $this->conversation->getActiveProfileIds();
        return (int)$profiles[random_int(0, count($profiles) - 1)];
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

    private function getRandomText(): string
    {
        return "command.looser.variant_" . random_int(0, self::LAST_NUMBER_TEXT);
    }

    private function checkStatistic(): void
    {
        if ($this->isNewWeek || $this->isNewMonth) {

            $statistic = new StatisticCommand(
                $this->logger,
                $this->entityManager,
                $this->saveConversationUseCase,
                $this->saveProfileUseCase,
                $this->dataGateway,
                $this->messageBuilder,
                $this->timeService,
                $this->message,
            );


            if ($this->isNewWeek) {
                $statistic->setType(StatisticCommand::TYPE_LOOSER_WEEK);
                $statistic->run();
            }

            if ($this->isNewMonth) {
                $statistic->setType(StatisticCommand::TYPE_LOOSER_MONTH);
                $statistic->run();
            }
        }
    }
}
