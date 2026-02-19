<?php

namespace App\Domain\ValueObject\Command;

use App\Application\UseCase\SaveConversationUseCase;
use App\Domain\Gateway\DataGatewayInterface;
use App\Domain\ValueObject\VK\MessageVK;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractCommand
{
    public const MAX_LENGTH = 100;

    protected int $id;
    protected int $peerId;
    protected int $fromId;
    protected string $text;
    protected int $date;
    protected int $conversationMessageId;

    public function __construct(
        protected LoggerInterface $logger,
        protected EntityManagerInterface $entityManager,
        /** @var SaveConversationUseCase */
        protected SaveConversationUseCase $saveConversationUseCase,
        protected DataGatewayInterface $dataGateway,
        MessageVK $message,
    ) {
        $this->id = $message->getId();
        $this->peerId = $message->getPeerId();
        $this->fromId = $message->getFromId();
        $this->text = $message->getText();
        $this->date = $message->getDate();
        $this->conversationMessageId = $message->getConversationMessageId();

        $this->logger->info('create command', ['id' => $message->getId(), 'peer_id' => $message->getPeerId(), 'text' => $message->getText(), 'from_id' => $message->getFromId(), 'conversation message id' => $message->getConversationMessageId()]);
    }

    abstract public function run(): void;

    public function getId(): int
    {
        return $this->id;
    }
    public function getPeerId(): int
    {
        return $this->peerId;
    }
    public function getFromId(): int
    {
        return $this->fromId;
    }
    public function getDate(): int
    {
        return $this->date;
    }
    public function getConversationMessageId(): int
    {
        return $this->conversationMessageId;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
