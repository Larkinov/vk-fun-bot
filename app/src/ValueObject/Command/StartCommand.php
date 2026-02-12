<?php

namespace App\ValueObject\Command;

use App\ValueObject\VK\MessageVK;
use Psr\Log\LoggerInterface;

class StartCommand extends AbstractCommand
{

    public function run(): void
    {
        $userName = $this->vkGateway->getUser($this->getFromId());
        $this->vkGateway->sendMessage('lalala - ' . $userName, $this->fromId);
    }
    // public function __construct(private LoggerInterface $logger, MessageVK $message)
    // {
    //     // $this->id = $object['message']->id;
    //     // $this->peerId = $object['message']->peer_id;
    //     // $this->fromId = $object['message']->from_id;
    //     // $this->text = $object['message']->text;
    //     // $this->date = $object['message']->date;
    //     // $this->conversationMessageId = $object['message']->conversation_message_id;

    //     $this->logger->info('create command', ['id' => $this->id, 'peer_id' => $this->peerId, 'text' => $this->text, 'from_id' => $this->fromId, 'conversation message id' => $this->conversationMessageId]);
    // }



    // public function getId(): int
    // {
    //     return $this->id;
    // }
    // public function getPeerId(): int
    // {
    //     return $this->peerId;
    // }
    // public function getFromId(): int
    // {
    //     return $this->fromId;
    // }
    // public function getDate(): int
    // {
    //     return $this->date;
    // }
    // public function getConversationMessageId(): int
    // {
    //     return $this->conversationMessageId;
    // }

    // public function getText(): string
    // {
    //     return $this->text;
    // }
}
