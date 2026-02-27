<?php

namespace App\Domain\ValueObject\Command;

use App\Application\UseCase\SaveConversationUseCase;
use App\Application\UseCase\SaveProfileUseCase;
use App\Domain\Builder\MessageBuilder;
use App\Domain\Entity\Conversation;
use App\Domain\Entity\Profile;
use App\Domain\Gateway\DataGatewayInterface;
use App\Domain\ValueObject\VK\MessageVK;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractCommand
{
    public const MAX_LENGTH = 100;

    protected int $id;
    protected int $peerId;
    protected int $fromId;
    protected ?int $memberId;
    protected int $date;
    protected int $conversationMessageId;

    protected array $context = [];
    protected ?Conversation $conversation;

    public function __construct(
        protected LoggerInterface $logger,
        protected EntityManagerInterface $entityManager,
        /** @var SaveConversationUseCase */
        protected SaveConversationUseCase $saveConversationUseCase,
        protected SaveProfileUseCase $saveProfileUseCase,
        protected DataGatewayInterface $dataGateway,
        protected MessageBuilder $messageBuilder,
        MessageVK $message,
    ) {
        $this->id = $message->getId();
        $this->peerId = $message->getPeerId();
        $this->fromId = $message->getFromId();
        $this->memberId = $message->getMemberId();
        $this->date = $message->getDate();
        $this->conversationMessageId = $message->getConversationMessageId();

        $this->context = [
            'id' => $message->getId(),
            'peer_id' => $message->getPeerId(),
            'from_id' => $message->getFromId(),
            'member_id' => $message->getMemberId(),
            'conversation message id' => $message->getConversationMessageId(),
            'command' => static::class,
        ];

        $this->conversation = $this->entityManager->getRepository(Conversation::class)->findOneBy(['peerId' => $this->peerId]);

        $this->logger->info('create command', $this->context);
    }

    abstract public function run(): void;
    abstract protected function getMessage(array $options = []): string;

    protected function getProfile(int $id): ?Profile
    {
        $this->logger->info('get profile from command', $this->context + ['profile id' => $id]);
        return $this->entityManager->getRepository(Profile::class)->findOneBy(['userId' => $id]);
    }

    protected function isNewConversation(): bool
    {
        if (is_null($this->conversation)) {
            $this->logger->info('new conversation', $this->context);
            return true;
        }
        return false;
    }
}
