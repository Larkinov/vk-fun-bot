<?php

namespace App\Application\UseCase;

use App\Application\Dto\CreateConversationDto;
use App\Application\Exceptions\ExceptionNotFoundAdmin;
use App\Application\Factory\FactoryConversation;
use App\Application\Factory\FactoryProfile;
use App\Domain\Entity\Conversation;
use App\Infrastructure\Exceptions\ExceptionVkGateway;
use App\Infrastructure\Gateway\VkGateway;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SaveConversationUseCase
{
    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private FactoryConversation $factoryConversation,
        private VkGateway $vkGateway,
    ) {}

    public function __invoke(int $peerId): ?Conversation
    {
        try {
            $this->logger->info('save conversation', ['peer_id' => $peerId]);

            $response = $this->vkGateway->getConversationMembers($peerId);

            $conversationDto = new CreateConversationDto($peerId, $response);

            $conversation = $this->entityManager->getRepository(Conversation::class)->findOneBy(['peerId' => $peerId]);

            if (is_null($conversation))
                $conversation = $this->factoryConversation->getInstance($conversationDto);

            return $conversation;
        } catch (ExceptionVkGateway|ExceptionNotFoundAdmin $th) {
            $this->logger->error('failed save conversation', ['message' => $th->getMessage(), 'trace' => $th->getTrace()]);
            return null;
        }
    }
}
