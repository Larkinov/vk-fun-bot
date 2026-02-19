<?php

namespace App\Application\UseCase;

use App\Application\Dto\CreateProfileDto;
use App\Application\Exceptions\ExceptionNotFoundAdmin;
use App\Application\Factory\FactoryProfile;
use App\Domain\Entity\Conversation;
use App\Domain\Entity\Profile;
use App\Infrastructure\Exceptions\ExceptionGateway;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SaveProfileUseCase
{
    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private FactoryProfile $factoryProfile,
    ) {}

    public function __invoke(CreateProfileDto $dto): ?Profile
    {
        try {
            $this->logger->info('save profile', ['peerId' => $dto->peerId, 'source' => 'conversation', 'userId' => $dto->userId]);

            $conversation = $this->entityManager->getRepository(Conversation::class)->findOneBy(['peerId' => $dto->peerId]);

            $profile = $this->entityManager->getRepository(Profile::class)->findOneBy(['peerId' => $dto->peerId, 'userId' => $dto->userId]);

            if (is_null($profile)) {
                $profile = $this->factoryProfile->getInstance($dto);
            }


            if (!in_array($dto->userId, $conversation->getProfileIds())) {
                $this->logger->info('save new profile in conversation', ['peerId' => $dto->peerId, 'source' => 'conversation', 'userId' => $dto->userId]);
                $conversation->setProfileIds([...$conversation->getProfileIds(), $dto->userId]);
                
                $this->entityManager->persist($profile);
                $this->entityManager->flush();
            }

            return $profile;
        } catch (ExceptionGateway | ExceptionNotFoundAdmin $th) {
            $this->logger->error('failed save profile', ['message' => $th->getMessage(), 'trace' => $th->getTrace()]);
            return null;
        }
    }
}
