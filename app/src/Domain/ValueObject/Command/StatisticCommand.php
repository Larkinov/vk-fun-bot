<?php

namespace App\Domain\ValueObject\Command;

use App\Domain\Entity\Profile;
use App\Domain\Exceptions\Command\Statistic\ExceptionEmptyStatistic;
use App\Domain\Exceptions\ExceptionUnknownStatisticType;
use App\Domain\ValueObject\Command\Data\LooserData;

class StatisticCommand extends AbstractCommand
{
    public const OPTION_STATISTIC_TYPE = 'statistic_type';

    public const TYPE_LOOSER_ALL_TIME = 'LOOSER_ALL';
    public const TYPE_LOOSER_MONTH = 'LOOSER_MONTH';
    public const TYPE_LOOSER_WEEK = 'LOOSER_WEEK';

    private string $type = self::TYPE_LOOSER_ALL_TIME;

    public static function isNewWeek(LooserData $looserData): bool
    {
        return $looserData->getLastWeekActive() !== date('W');
    }

    public static function isNewMonth(LooserData $looserData): bool
    {
        return $looserData->getLastMonthActive() !== date('n');
    }

    public function setType(string $type)
    {
        if (!in_array($type, [self::TYPE_LOOSER_ALL_TIME, self::TYPE_LOOSER_MONTH, self::TYPE_LOOSER_WEEK]))
            throw new ExceptionUnknownStatisticType($type);

        $this->type = $type;
    }

    public function run(): void
    {
        if ($this->isNewConversation())
            return;

        $this->dataGateway->sendMessage($this->getMessage(), $this->peerId);
    }

    protected function getMessage(array $options = []): string
    {
        switch ($this->type) {
            case self::TYPE_LOOSER_ALL_TIME:
                return $this->messageBuilder
                    ->setMessageId('command.statistic.looser_all')
                    ->setAdditionalText($this->getMessageLooserStats())
                    ->build();
            case self::TYPE_LOOSER_MONTH:
                return $this->messageBuilder
                    ->setMessageId('command.statistic.looser_month')
                    ->setAdditionalText($this->getMessageLooserStats("\n***\n"))
                    ->build();
            case self::TYPE_LOOSER_WEEK:
                return $this->messageBuilder
                    ->setMessageId('command.statistic.looser_week')
                    ->setAdditionalText($this->getMessageLooserStats("\n***\n"))
                    ->build();
            default:
                throw new ExceptionUnknownStatisticType($this->type);
        }
    }

    private function getMessageLooserStats(): string
    {
        $looserStats = $this->getLooserStats();

        uasort($looserStats, fn($a, $b) => $b['count'] <=> $a['count']);

        $message = "\n";

        $i = 0;

        foreach ($looserStats as $data) {
            if ($i === 0)
                $message .=  "🏆🥇";
            if ($i === 1)
                $message .= "🥈";
            if ($i === 2)
                $message .= "🥉";

            $message .= " $data[name] $data[lastname] - $data[count]\n";
            $i++;
        }

        return $message;
    }

    private function getLooserStats(): array
    {
        $statisticLooser = $this->conversationDetails->getLooserData()->getProfiles();

        if (isset($statisticLooser[$this->type]))
            $statisticLooser = $statisticLooser[$this->type];


        $profilesData = $this->entityManager->getRepository(Profile::class)->findAll();

        if (empty($profilesData))
            throw new ExceptionEmptyStatistic;

        $loosersData = [];

        foreach ($profilesData as $profile) {
            if (!$profile instanceof Profile)
                continue;

            $loosersData[$profile->getUserId()] = [
                'count' => $statisticLooser[$profile->getUserId()] ?? 0,
                'name' => $profile->getName(),
                'lastname' => $profile->getLastname()
            ];
        }
        $this->logger->info('get looser statistic', ['statistic' => $loosersData]);

        return $loosersData;
    }
}
