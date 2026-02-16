<?php

namespace App\Application\Dto;

use App\Infrastructure\Exceptions\ExceptionVkGateway;

class CreateConversationDto
{
    public readonly int $count;
    public readonly array $profileIds;
    public readonly array $items;

    public function __construct(public readonly int $peerId, array $response)
    {

        if (!isset(
            $response['response']['profiles'],
            $response['response']['items'],
            $response['response']['count']
        ))
            throw new ExceptionVkGateway('not found params "profiles","items", "count" in getConversationMembers');

        $profileIds = [];

        foreach ($response['response']['profiles'] as $value) {
            $profileIds[] = $value['id'];
        }

        $this->profileIds = $profileIds;
        $this->items = $response['response']['items'];
        $this->count = $response['response']['count'];
    }
}
