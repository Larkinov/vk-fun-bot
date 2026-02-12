<?php

namespace App\Domain\ValueObject\VK;

use App\Domain\ValueObject\Command\AbstractCommand;
use Psr\Log\LoggerInterface;

class MessageVK
{

    private int $id;
    private int $peerId;
    private int $fromId;
    private string $text;
    private int $date;
    private int $conversationMessageId;

    public function __construct(private LoggerInterface $logger, array $object)
    {
        $this->id = $object['message']->id;
        $this->peerId = $object['message']->peer_id;
        $this->fromId = $object['message']->from_id;
        $this->text = $object['message']->text;
        $this->date = $object['message']->date;
        $this->conversationMessageId = $object['message']->conversation_message_id;

        $this->logger->info('create message VK', ['id' => $this->id, 'peer_id' => $this->peerId, 'text' => $this->text, 'from_id' => $this->fromId, 'conversation message id' => $this->conversationMessageId]);
    }

    public static function isCorrectCommand(LoggerInterface $logger, string $text): bool
    {
        $pattern = '/^\/[a-z]+$/';

        if (!preg_match($pattern, $text)) {
            $logger->info('skip message', ['reason' => 'is not command']);
            return false;
        }

        if (strlen($text) > AbstractCommand::MAX_LENGTH) {
            $logger->info('skip message', ['reason' => 'too long length']);
            return false;
        }

        return true;
    }

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
