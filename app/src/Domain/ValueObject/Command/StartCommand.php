<?php

namespace App\Domain\ValueObject\Command;

use App\Application\Dto\CreateConversationDto;
use App\Domain\Entity\Conversation;

class StartCommand extends AbstractCommand
{

    public function run(): void
    {
        $userName = $this->vkGateway->getUser($this->getFromId());
        // $this->vkGateway->sendMessage('lalalalala - ' . $userName, $this->fromId);

        $response = $this->vkGateway->getConversationMembers($this->peerId);

        $this->logger->info('members', ['111' => $response]);
        $conversationDto = new CreateConversationDto($this->peerId, $response);

        $conversation = $this->entityManager->getRepository(Conversation::class)->findOneBy(['peerId'=>$this->peerId]);

        if (is_null($conversation))
            $conversation = $this->factoryConversation->getInstance($conversationDto);

        $this->logger->info('get conversation', ['conv' => $conversation]);
        // $this->conversationRepository->get


    }
}
