<?php
declare(strict_types=1);
namespace App\Application\Factory;

use App\Application\Dto\MessageVK;
use Psr\Log\LoggerInterface;

class FactoryMessageVK
{
    public function __construct(private LoggerInterface $logger) {}

    public function getInstance(array $object): MessageVK
    {
        return new MessageVK($this->logger, $object);
    }
}
