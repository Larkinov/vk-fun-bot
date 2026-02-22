<?php

namespace App\Domain\ValueObject\VK;

use App\Domain\Exceptions\ExceptionNotValidCommand;
use App\Domain\ValueObject\Command\AbstractCommand;
use Psr\Log\LoggerInterface;

class MessageVK
{
    private const TYPE_VALUE_NEW_USER = 'chat_invite_user';
    private const TYPE_VALUE_KICK_USER = 'chat_kick_user';

    private const COMMAND_NEW_USER = 'NewUser';
    private const COMMAND_KICK_USER = 'KickUser';

    private string $command = 'unknown';
    private bool $isCorrectCommand;

    private int $id;
    private int $peerId;
    private int $fromId;
    private ?string $text;
    private ?int $memberId;
    private int $date;
    private int $conversationMessageId;

    public function __construct(private LoggerInterface $logger, array $object)
    {
        $this->id = $object['message']->id;
        $this->peerId = $object['message']->peer_id;
        $this->fromId = $object['message']->from_id;
        $this->text = $object['message']->text ?? null;
        $this->memberId = $object['message']->action->member_id ?? null;
        $this->date = $object['message']->date;
        $this->conversationMessageId = $object['message']->conversation_message_id;

        $this->calculateCommand($object['message']->action->type ?? null);

        $this->logger->info('create message VK', [
            'id' => $this->id,
            'type' => $object['message']->action->type ?? null,
            'peer_id' => $this->peerId,
            'command' => $this->command,
            'from_id' => $this->fromId,
            'member_id' => $this->memberId,
            'conversation message id' => $this->conversationMessageId
        ]);
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getPeerId(): int
    {
        return $this->peerId;
    }

    public function getMemberId(): ?int
    {
        return $this->memberId;
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

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function isCorrectCommand(): bool
    {
        return $this->isCorrectCommand;
    }

    private function calculateCommand(?string $type): void
    {
        if (!empty($this->text)) {
            $pattern = '/^\/[a-z]+$/';

            if (strlen($this->text) > AbstractCommand::MAX_LENGTH) {
                $this->logger->info('skip message', ['reason' => 'text too long length']);
                throw new ExceptionNotValidCommand;
            }

            if (!preg_match($pattern, $this->text)) {
                $this->logger->info('skip message', ['reason' => 'text is not command', 'text' => $this->text]);
                throw new ExceptionNotValidCommand;
            }


            $this->isCorrectCommand = true;
            $this->command = ucfirst(substr($this->text, 1));

            return;
        }

        if ($type === self::TYPE_VALUE_NEW_USER) {
            $this->isCorrectCommand = true;
            $this->command = self::COMMAND_NEW_USER;
            return;
        }
        if ($type === self::TYPE_VALUE_KICK_USER) {
            $this->isCorrectCommand = true;
            $this->command = self::COMMAND_KICK_USER;
            return;
        }

        $this->logger->info('skip message', ['reason' => 'unknown type', 'type' => $type]);
        throw new ExceptionNotValidCommand;
    }
}
