<?php

namespace App\Infrastructure\Gateway;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Infrastructure\Exception\ExceptionNullParamConfiguration;
use App\Infrastructure\Exception\ExceptionVkGateway;

class VkGateway
{

    private const URL = "https://api.vk.ru/method";

    private string $token;
    private string $version;

    public function __construct(private HttpClientInterface $client, private LoggerInterface $logger)
    {
        $this->token = $_ENV['VK_TOKEN'] ?? null;
        $this->version = $_ENV['VERSION'] ?? null;

        if (is_null($this->token))
            throw new ExceptionNullParamConfiguration('token');
        if (is_null($this->version))
            throw new ExceptionNullParamConfiguration('token');
    }

    public function sendMessage(string $message, int $peerId): void
    {
        $response = $this->client->request(
            'GET',
            self::URL . '/message.send',
            [
                'query' => [
                    'message' => $message,
                    'peer_id' => $peerId,
                    'access_token' => $this->token,
                    'v' => $this->version,
                    'random_id' => '0'
                ]
            ]
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode === 200)
            return;

        $this->logger->error('failed send message', ['content' => $response->toArray(false), 'status code' => $statusCode]);
    }

    public function getUser(int $userId)
    {

        $response = $this->client->request(
            'GET',
            self::URL . '/users.get',
            [
                'query' => [
                    'user_ids' => $userId,
                    'access_token' => $this->token,
                    'v' => $this->version,
                ]
            ]
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200) {
            $this->logger->error('failed get users', ['content' => $response->toArray(false), 'status code' => $statusCode]);
            throw new ExceptionVkGateway('failed get users');
        }

        $this->logger->info('get users', ['response' => $response->toArray(false)]);

        return $response->toArray(false)['response'][0]['first_name'];
    }
}
