<?php

namespace App\Infrastructure\Gateway;

use App\Domain\Gateway\DataGatewayInterface;
use App\Infrastructure\Exceptions\ExceptionAccessGateway;
use App\Infrastructure\Exceptions\ExceptionNullParamConfiguration;
use App\Infrastructure\Exceptions\ExceptionGateway;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class VkGateway implements DataGatewayInterface
{

    private const URL = "https://api.vk.ru/method";
    private const ERROR_CODE_ACCESS = 917;

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
            self::URL . '/messages.send',
            [
                'query' => [
                    'message' => $message,
                    'peer_id' => $peerId,
                    'access_token' => $this->token,
                    'v' => $this->version,
                    'random_id' => microtime(true) * 10000,
                ]
            ]
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {
            $this->checkError($response->toArray(false), __FUNCTION__);
            return;
        }

        $this->logger->error('failed send message', ['content' => $response->toArray(false), 'status code' => $statusCode]);
        throw new ExceptionGateway('failed send message');
    }

    public function getUser(int $userId): array
    {

        $response = $this->client->request(
            'GET',
            self::URL . '/users.get',
            [
                'query' => [
                    'user_ids' => $userId,
                    'access_token' => $this->token,
                    'v' => $this->version,
                    'fields'=>'sex,screen_name',
                ]
            ]
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200) {
            $this->logger->error('failed get users', ['content' => $response->toArray(false), 'status code' => $statusCode]);
            throw new ExceptionGateway('failed get users');
        }

        $this->checkError($response->toArray(false), __FUNCTION__);

        $this->logger->info('get users', ['response' => $response->toArray(false)]);

        return $response->toArray(false)['response'][0];
    }

    public function getConversationMembers(int $peerId): array
    {
        $response = $this->client->request(
            'GET',
            self::URL . '/messages.getConversationMembers',
            [
                'query' => [
                    'peer_id' => $peerId,
                    'access_token' => $this->token,
                    'v' => $this->version,
                ]
            ]
        );

        $statusCode = $response->getStatusCode();

        $response = $response->toArray(false);

        if ($statusCode === 200) {
            $this->checkError($response, __FUNCTION__);
            $this->logger->info('get conversation members', ['response' => $response]);
            return $response;
        }

        $this->logger->error('failed get conversation members', ['content' => $response, 'status code' => $statusCode]);
        throw new ExceptionGateway('failed get conversation members');
    }

    private function checkError(array $response, string $funcName): void
    {
        $this->checkErrorAccess($response, $funcName);

        if (!isset($response['error']))
            return;

        $this->logger->error('failed response from VK', ['response' => $response, 'function' => $funcName]);

        throw new ExceptionGateway('failed response from VK');
    }

    private function checkErrorAccess(array $response, string $funcName): void
    {
        if (!isset($response['error']['error_code']))
            return;

        if ($response['error']['error_code'] !== self::ERROR_CODE_ACCESS)
            return;

        $this->logger->error('dont have access', ['response' => $response, 'function' => $funcName]);

        throw new ExceptionAccessGateway;
    }
}
