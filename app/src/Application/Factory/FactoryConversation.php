<?php

namespace App\Application\Factory;

use App\Application\Dto\CreateConversationDto;
use App\Domain\Entity\Conversation;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class FactoryConversation
{
    public function __construct(private LoggerInterface $logger, private EntityManagerInterface $entityManager) {}
    public function getInstance(CreateConversationDto $dto): Conversation
    {
        $this->logger->info('create conversation', ['peer_id' => $dto->peerId]);
        $conversation = new Conversation();
        $conversation->setPeerId($dto->peerId);
        $conversation->setMemberCount($dto->count);

        $this->entityManager->persist($conversation);

        $this->entityManager->flush();

        return $conversation;
    }
}
