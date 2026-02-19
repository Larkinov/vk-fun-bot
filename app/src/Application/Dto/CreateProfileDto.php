<?php

namespace App\Application\Dto;

use App\Infrastructure\Exceptions\ExceptionGateway;

class CreateProfileDto
{
    public readonly int $userId;
    public readonly bool $isMale;
    public readonly string $nickname;
    public readonly string $name;
    public readonly string $lastname;

    public function __construct(
        public readonly int $peerId,
        array $profile
    ) {

        if (!isset(
            $profile['id'],
            $profile['sex'],
            $profile['screen_name'],
            $profile['first_name'],
            $profile['last_name'],
        ))
            throw new ExceptionGateway('not found required params profile in getConversationMembers');

        $this->userId = $profile['id'];
        $this->isMale = $profile['sex'] === 2 ? true : false;
        $this->nickname = $profile['screen_name'];
        $this->name = $profile['first_name'];
        $this->lastname = $profile['last_name'];
    }
}
