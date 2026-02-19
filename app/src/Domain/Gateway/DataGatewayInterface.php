<?php

namespace App\Domain\Gateway;

interface DataGatewayInterface
{
    public function sendMessage(string $message, int $peerId): void;

    public function getUser(int $userId): array;

    public function getConversationMembers(int $peerId): array;
}
