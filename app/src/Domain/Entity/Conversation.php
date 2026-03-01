<?php

namespace App\Domain\Entity;

use App\Infrastructure\Repository\ConversationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'member_count', type: Types::SMALLINT)]
    private ?int $memberCount = null;

    #[ORM\Column(name: 'peer_id')]
    private ?int $peerId = null;

    #[ORM\Column(name: 'admin_id')]
    private ?int $adminId = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $profileIds = [];

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    private ?array $inactiveProfileIds = null;

    #[ORM\OneToOne(mappedBy: 'conversation', targetEntity: ConversationDetails::class, cascade: ['persist', 'remove'])]
    private ?ConversationDetails $details = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDetails(): ?ConversationDetails
    {
        return $this->details;
    }

    public function setDetails(ConversationDetails $details): static
    {
        if ($details->getConversation() !== $this) {
            $details->setConversation($this);
        }

        $this->details = $details;

        return $this;
    }

    public function getAdminId(): ?int
    {
        return $this->adminId;
    }

    public function setAdminId(int $adminId): static
    {
        $this->adminId = $adminId;
        return $this;
    }

    public function getMemberCount(): ?int
    {
        return $this->memberCount;
    }

    public function setMemberCount(int $memberCount): static
    {
        $this->memberCount = $memberCount;

        return $this;
    }

    public function getPeerId(): ?int
    {
        return $this->peerId;
    }

    public function setPeerId(int $peerId): static
    {
        $this->peerId = $peerId;

        return $this;
    }

    public function getProfileIds(): array
    {
        return $this->profileIds;
    }

    public function setProfileIds(array $ids): static
    {
        $this->profileIds = $ids;

        return $this;
    }

    public function getActiveProfileIds(): array
    {
        return array_values(array_diff($this->profileIds, $this->inactiveProfileIds ?? []));
    }

    public function getInactiveProfileIds(): ?array
    {
        return $this->inactiveProfileIds;
    }

    public function setInactiveProfileIds(array $ids): static
    {
        $this->inactiveProfileIds = $ids;

        return $this;
    }

    public function getActiveMemberCount(): int
    {
        return count($this->getActiveProfileIds());
    }
}
