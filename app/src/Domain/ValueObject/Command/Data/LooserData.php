<?php

namespace App\Domain\ValueObject\Command\Data;

class LooserData
{
    public function __construct(
        private array $profiles,
        private int $lastActiveAt,
    ) {}

    public function getProfiles(): array
    {
        return $this->profiles;
    }

    public function getLastActive():int{
        return $this->lastActiveAt;
    }

    public function incrementLooser(int $idLooser): self
    {
        $this->lastActiveAt = time();

        if (isset($this->profiles[$idLooser]))
            $this->profiles[$idLooser]++;
        else
            $this->profiles[$idLooser] = 1;

        return new self($this->profiles, $this->lastActiveAt);
    }
}
