<?php
declare(strict_types=1);
namespace App\Domain\ValueObject\Command;

use App\Domain\Builder\MessageBuilder;

class StartCommand extends AbstractCommand
{

    public function run(): void
    {
        if ($this->isNewConversation()) {
            $this->logger->info('first init command', $this->context);
            ($this->saveConversationUseCase)($this->peerId);

            $this->dataGateway->sendMessage($this->getMessage(['started' => 'first']), $this->peerId);
            return;
        }

        $this->logger->info('repeat init command', $this->context);
        $this->dataGateway->sendMessage($this->getMessage(['started' => 'repeat']), $this->peerId);
    }

    protected function getMessage(array $options = []): string
    {
        return $this->messageBuilder
            ->setMessageId('command.start')
            ->addNewOptions('started', $options['started'])
            ->setDomain(MessageBuilder::DOMAIN_MAIN)
            ->build();
    }
}
