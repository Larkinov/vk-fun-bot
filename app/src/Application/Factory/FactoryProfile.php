<?php

namespace App\Application\Factory;

use App\Application\Dto\CreateProfileConversationDto;
use App\Application\Exceptions\ExceptionNotFoundAdmin;
use App\Domain\Entity\Conversation;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class FactoryProfile
{
    public function __construct(private LoggerInterface $logger, private EntityManagerInterface $entityManager) {}

    // public function getInstanceFromConversation(CreateProfileConversationDto $dto): Conversation
    // {
    //     $this->logger->info('create profile from conversation', ['peer_id' => $dto->peerId]);

    //     // $conversation = new Conversation();
    //     // $conversation->setPeerId($dto->peerId);
    //     // $conversation->setMemberCount($dto->count);

    //     // $this->entityManager->persist($conversation);

    //     // $this->entityManager->flush();

    //     return $conversation;
    // }

}
