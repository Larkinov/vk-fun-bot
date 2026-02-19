<?php

namespace App\Application\Dto;

use App\Infrastructure\Exceptions\ExceptionGateway;

class CreateConversationDto
{
    public readonly int $count;

    /** @var array<CreateProfileDto>  */
    public readonly array $profiles;
    public readonly array $profileIds;
    public readonly array $items;

    public function __construct(public readonly int $peerId, array $response)
    {

        if (!isset(
            $response['response']['profiles'],
            $response['response']['items'],
            $response['response']['count']
        ))
            throw new ExceptionGateway('not found params "profiles","items", "count" in getConversationMembers');

        $profileIds = [];
        $profiles = [];

        foreach ($response['response']['profiles'] as $value) {
            $profileIds[] = $value['id'];
            $profiles[$value['id']] = new CreateProfileDto($peerId, $value);
        }

        $this->profileIds = $profileIds;
        $this->profiles = $profiles;
        $this->items = $response['response']['items'];
        $this->count = $response['response']['count'];
    }
}
