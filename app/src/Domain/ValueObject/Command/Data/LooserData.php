<?php
declare(strict_types=1);
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

    public function setProfiles(array $profiles): self
    {
        return new self(
            $profiles,
            $this->lastActiveAt,
            $this->lastWeekActive,
            $this->lastMonthActive
        );
    }

    public function incrementLooser(int $idLooser): self
    {
        $this->lastActiveAt = time();
        $this->lastWeekActive = (int)date('W');
        $this->lastMonthActive = (int)date('n');

        $this->incrementLooserStatistic(StatisticCommand::TYPE_LOOSER_ALL_TIME, $idLooser);
        $this->incrementLooserStatistic(StatisticCommand::TYPE_LOOSER_MONTH, $idLooser);
        $this->incrementLooserStatistic(StatisticCommand::TYPE_LOOSER_WEEK, $idLooser);

        return new self(
            $this->profiles,
            $this->lastActiveAt,
            $this->lastWeekActive,
            $this->lastMonthActive
        );
    }

    private function incrementLooserStatistic(string $type, int $idLooser): void
    {
        if (isset($this->profiles[$type][$idLooser]))
            $this->profiles[$type][$idLooser]++;
        else
            $this->profiles[$type][$idLooser] = 1;
    }
}
