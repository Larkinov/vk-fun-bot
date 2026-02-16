<?php

namespace App\Application\Dto;

use App\Infrastructure\Exception\ExceptionVkGateway;

class CreateConversationDto
{
    public readonly int $count;
    public readonly array $profiles;

    public function __construct(public readonly int $peerId, array $response)
    {

        if (!isset($response['response']['profiles'], $response['response']['count']))
            throw new ExceptionVkGateway('not found params "profiles" or "count" in getConversationMembers');

        $this->profiles = $response['response']['profiles'];
        $this->count = $response['response']['count'];
    }
}
