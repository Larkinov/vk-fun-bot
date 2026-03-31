<?php
declare(strict_types=1);
namespace App\Application\UseCase\Command;

use App\Domain\Builder\MessageBuilder;

class HelpCommand extends AbstractCommand
{

    public function run(): void
    {
        if ($this->isNewConversation())
            return;

        $this->dataGateway->sendMessage($this->getMessage(), $this->peerId);
    }

    public static function getRussianAlias(): string
    {
        return 'помощь';
    }

    protected function getMessage(array $options = []): string
    {
        return $this->messageBuilder
            ->setMessageId('command.help')
            ->setDomain(MessageBuilder::DOMAIN_MAIN)
            ->build();
    }
}
