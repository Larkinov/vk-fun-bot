<?php

namespace App\Application\UseCase;

use App\Application\Dto\CreateConversationDto;
use App\Application\Exceptions\ExceptionNotFoundAdmin;
use App\Application\Factory\FactoryConversation;
use App\Application\Factory\FactoryProfile;
use App\Domain\Entity\Conversation;
use App\Domain\Entity\Profile;
use App\Infrastructure\Exceptions\ExceptionVkGateway;
use App\Infrastructure\Gateway\VkGateway;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SaveProfileUseCase
{
    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private VkGateway $vkGateway,
    ) {}

    // public function __invoke(int $userId): ?Profile
    // {
    //     try {
    //         $this->logger->info('save profile', ['peer_id' => $userId]);

    //         $response = $this->vkGateway->getConversationMembers($userId);

    //         $conversationDto = new CreateConversationDto($userId, $response);

    //         $conversation = $this->entityManager->getRepository(Conversation::class)->findOneBy(['userId' => $userId]);

    //         // if (is_null($conversation))
    //             $conversation = $this->factoryConversation->getInstance($conversationDto, $factoryProfile);

    //         return $conversation;
    //     } catch (ExceptionVkGateway|ExceptionNotFoundAdmin $th) {
    //         $this->logger->error('failed save conversation', ['message' => $th->getMessage(), 'trace' => $th->getTrace()]);
    //         return null;
    //     }
    // }
}
