<?php

namespace App\Domain\ValueObject\Command\Data;

use App\Domain\ValueObject\Command\StatisticCommand;

class LooserData
{
    public function __construct(
        private array $profiles,
        private int $lastActiveAt,
        private int $lastWeekActive,
        private int $lastMonthActive,
    ) {}

    public function getProfiles(): array
    {
        return $this->profiles;
    }

    public function getLastActive(): int
    {
        return $this->lastActiveAt;
    }
    public function getLastWeekActive(): int
    {
        return $this->lastWeekActive;
    }
    public function getLastMonthActive(): int
    {
        return $this->lastMonthActive;
    }

    public function incrementLooser(int $idLooser): self
    {
        $this->lastActiveAt = time();
        $this->lastWeekActive = date('W');
        $this->lastMonthActive = date('n');

        $this->updateLooserStatistic(StatisticCommand::TYPE_LOOSER_ALL_TIME, $idLooser);
        $this->updateLooserStatistic(StatisticCommand::TYPE_LOOSER_MONTH, $idLooser);
        $this->updateLooserStatistic(StatisticCommand::TYPE_LOOSER_WEEK, $idLooser);

        return new self(
            $this->profiles,
            $this->lastActiveAt,
            $this->lastWeekActive,
            $this->lastMonthActive
        );
    }

    private function updateLooserStatistic(string $type, int $idLooser): void
    {
        if (isset($this->profiles[$type][$idLooser]))
            $this->profiles[$type][$idLooser]++;
        else
            $this->profiles[$type][$idLooser] = 1;
    }
}
