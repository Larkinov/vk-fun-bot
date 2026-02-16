<?php

namespace App\Application\Factory;

use App\Application\Dto\CreateConversationDto;
use App\Application\Exceptions\ExceptionNotFoundAdmin;
use App\Domain\Entity\Conversation;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class FactoryConversation
{
    public function __construct(private LoggerInterface $logger, private EntityManagerInterface $entityManager) {}

    public function getInstance(CreateConversationDto $dto): Conversation
    {
        $this->logger->info('create conversation', ['peer_id' => $dto->peerId]);

        $adminId = $this->getAdminId($dto->items, $dto->peerId);

        $conversation = new Conversation();
        $conversation->setPeerId($dto->peerId);
        $conversation->setMemberCount($dto->count);
        $conversation->setAdminId($adminId);
        $conversation->setProfileIds($dto->profileIds);

        $this->entityManager->persist($conversation);

        $this->entityManager->flush();

        return $conversation;
    }

    private function getAdminId(array $items, int $peerId): int
    {
        foreach ($items as $value) {
            if (isset($value['is_admin']) && abs($value['member_id']) !== $_ENV['GROUP_ID'])
                return $value['member_id'];
        }

        throw new ExceptionNotFoundAdmin($peerId);
    }
}
