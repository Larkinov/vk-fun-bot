<?php

namespace App\Application\Dto;

use App\Infrastructure\Exceptions\ExceptionVkGateway;

class CreateProfileConversationDto
{
    public readonly int $userId;
    public readonly bool $isMale;
    public readonly string $nickname;
    public readonly string $name;
    public readonly string $lastname;

    public function __construct(
        public readonly int $peerId,
        public readonly bool $isAdmin,
        array $profile
    ) {
        if (!isset(
            $profile['profile']['id'],
            $profile['profile']['sex'],
            $profile['profile']['screen_name'],
            $profile['profile']['first_name'],
            $profile['profile']['last_name'],
        ))
            throw new ExceptionVkGateway('not found required params profile in getConversationMembers');

        $this->userId = $profile['profile']['id'];
        $this->isMale = $profile['profile']['sex'] === 2 ? true : false;
        $this->nickname = $profile['profile']['screen_name'];
        $this->name = $profile['profile']['first_name'];
        $this->lastname = $profile['profile']['last_name'];
    }
}
