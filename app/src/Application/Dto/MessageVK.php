<?php

declare(strict_types=1);

namespace App\Application\Dto;

use App\Application\Exceptions\Dto\ExceptionNotValidCommand;
use App\Application\UseCase\Command\AbstractCommand;
use Psr\Log\LoggerInterface;

class MessageVK
{
    private const TYPE_VALUE_NEW_USER = 'chat_invite_user';
    private const TYPE_VALUE_KICK_USER = 'chat_kick_user';

    private const COMMAND_NEW_USER = 'NewUser';
    private const COMMAND_KICK_USER = 'KickUser';

    private string $command = 'unknown';

    private int $id;
    private int $peerId;
    private int $fromId;
    private ?string $text = null;
    private ?int $memberId = null;
    private int $date;
    private int $conversationMessageId;

    public function __construct(private LoggerInterface $logger, array $object)
    {
        $this->id = $object['message']->id;
        $this->peerId = $object['message']->peer_id;
        $this->fromId = $object['message']->from_id;
        
        if (!empty($object['message']->text))
            $this->text = $object['message']->text;

        if (!empty($object['message']->action->type))
           $type = $object['message']->action->type;
        else
            $type = null;

        if (!empty($object['message']->action->member_id))
            $this->memberId = $object['message']->action->member_id;

        $this->date = $object['message']->date;
        $this->conversationMessageId = $object['message']->conversation_message_id;

        $this->calculateCommand($type);

        $this->logger->info('create message VK', [
            'id' => $this->id,
            'type' => $type,
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

    private function calculateCommand(?string $type): void
    {
        if (!empty($this->text)) {
            $pattern = '/^\/[a-zA-Zа-яА-ЯёЁ]+$/u';

            if (strlen($this->text) > AbstractCommand::MAX_LENGTH_COMMAND) {
                $this->logger->info('skip message', ['reason' => 'text too long length']);
                throw new ExceptionNotValidCommand;
            }

            if (!preg_match($pattern, $this->text)) {
                $this->logger->info('skip message', ['reason' => 'text is not command', 'text' => $this->text]);
                throw new ExceptionNotValidCommand;
            }

            $this->command = ucfirst(strtolower(substr($this->text, 1)));
            return;
        }

        if ($type === self::TYPE_VALUE_NEW_USER) {
            $this->command = self::COMMAND_NEW_USER;
            return;
        }

        if ($type === self::TYPE_VALUE_KICK_USER) {
            $this->command = self::COMMAND_KICK_USER;
            return;
        }

        $this->logger->info('skip message', ['reason' => 'unknown type', 'type' => $type]);
        throw new ExceptionNotValidCommand;
    }
}
