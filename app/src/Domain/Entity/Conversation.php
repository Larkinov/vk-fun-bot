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

    public function getId(): ?int
    {
        return $this->id;
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

    public function setProfileIds(array $profileIds): static
    {
        $this->profileIds = $profileIds;

        return $this;
    }
}
