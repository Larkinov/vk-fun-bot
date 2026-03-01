<?php

namespace App\Domain\Entity;

use App\Domain\ValueObject\Command\Data\LooserData;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ConversationDetails
{
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Conversation::class, inversedBy: 'details')]
    #[ORM\JoinColumn(name: 'peer_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Conversation $conversation = null;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private ?string $activatedAt = null;

    #[ORM\Column(type: 'looser_data', nullable: true)]
    private ?LooserData $looserData = null;

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(Conversation $conversation): self
    {
        $this->conversation = $conversation;
        return $this;
    }

    public function setActivatedAt(): self
    {
        $this->activatedAt = (string) time();
        return $this;
    }

    public function getActivatedAt(): ?int
    {
        return $this->activatedAt !== null ? (int) $this->activatedAt : null;
    }

    public function getLooserData(): ?LooserData
    {
        return $this->looserData;
    }

    public function setLooserData(LooserData $looserData): static
    {
        $this->looserData = $looserData;

        return $this;
    }
}
