<?php

namespace App\Domain\Builder;

use App\Domain\Entity\Profile;
use App\Domain\Exceptions\ExceptionUnknownMessageDomain;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageBuilder
{
    public const DOMAIN_MAIN = 'main';
    public const DOMAIN_LOOSER = 'looser';

    private string $id;
    private array $options;
    private string $domain;

    public function __construct(
        private TranslatorInterface $translator,
        private LoggerInterface $logger,
    ) {
        $this->options['botName'] = $_ENV['BOT_NAME'];
        $this->domain = self::DOMAIN_MAIN;
    }

    public function setMessageId(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function setDomain(string $domain): static
    {
        if (!in_array($domain, [self::DOMAIN_LOOSER, self::DOMAIN_MAIN]))
            throw new ExceptionUnknownMessageDomain($domain);

        $this->domain = $domain;
        return $this;
    }

    public function addNewOptions(string $key, string $value): static
    {
        $this->options[$key] = $value;
        return $this;
    }

    public function setNames(string $name, string $lastname): static
    {
        $this->options['name'] = $name;
        $this->options['lastname'] = $lastname;
        return $this;
    }

    public function setProfile(Profile $profile): static
    {
        $this->options['name'] = $profile->getName();
        $this->options['lastname'] = $profile->getLastname();
        $this->options['gender'] = $profile->isMale() ? 'male' : 'female';
        return $this;
    }

    public function build(): string
    {
        $message =  $this->translator->trans($this->id, $this->options, $this->domain);
        $this->logger->info('complete build message', ['message' => $message]);
        return $message;
    }
}
