<?php

namespace App\Application\Factory;

use App\Application\Dto\CreateProfileDto;
use App\Domain\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class FactoryProfile
{
    public function __construct(private LoggerInterface $logger, private EntityManagerInterface $entityManager) {}

    public function getInstance(CreateProfileDto $dto): Profile
    {
        $this->logger->info('create profile', ['peerId' => $dto->peerId, 'userId' => $dto->userId]);

        $profile = new Profile;

        $profile->setUserId($dto->userId);
        $profile->setPeerId($dto->peerId);
        $profile->setMale($dto->isMale);
        $profile->setNickname($dto->nickname);
        $profile->setName($dto->name);
        $profile->setLastname($dto->lastname);

        $this->entityManager->persist($profile);

        $this->entityManager->flush();

        return $profile;
    }
}
