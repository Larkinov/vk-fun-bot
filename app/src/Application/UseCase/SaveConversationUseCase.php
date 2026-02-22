<?php

namespace App\Application\UseCase;

use App\Application\Dto\CreateConversationDto;
use App\Application\Exceptions\ExceptionNotFoundAdmin;
use App\Application\Factory\FactoryConversation;
use App\Domain\Entity\Conversation;
use App\Domain\Gateway\DataGatewayInterface;
use App\Infrastructure\Exceptions\ExceptionGateway;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SaveConversationUseCase
{
    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private SaveProfileUseCase $saveProfileUseCase,
        private FactoryConversation $factoryConversation,
        private DataGatewayInterface $dataGateway,
    ) {}

    public function __invoke(int $peerId): ?Conversation
    {
        try {
            $this->logger->info('save conversation', ['peer_id' => $peerId]);

            $response = $this->dataGateway->getConversationMembers($peerId);

            $conversationDto = new CreateConversationDto($peerId, $response);

            $conversation = $this->entityManager->getRepository(Conversation::class)->findOneBy(['peerId' => $peerId]);

            if (is_null($conversation)){
                $conversation = $this->factoryConversation->getInstance($conversationDto);
                foreach ($conversationDto->profiles as $profile) {
                    ($this->saveProfileUseCase)($conversation,$profile);
                }
            }

            return $conversation;
        } catch (ExceptionGateway|ExceptionNotFoundAdmin $th) {
            $this->logger->error('failed save conversation', ['message' => $th->getMessage(), 'trace' => $th->getTrace()]);
            return null;
        }
    }
}
