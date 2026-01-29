<?php

namespace App\Service;

use Generator\Skeleton\skeleton\base\src\VK\CallbackApi\VKCallbackApiServerHandler;
use Psr\Log\LoggerInterface;

class ServerVkHandler extends VKCallbackApiServerHandler
{
    private const SECRET = '6kiKyWMZS979';
    private const GROUP_ID = 222395969;
    private const CONFIRMATION_TOKEN = 'e227da2b';
    private const VERSION = '5.199';

    public function __construct(private LoggerInterface $logger) {}

    public function messageNew(int $group_id, ?string $secret, array $object)
    {
        $this->logger->info('message new', ['id' => $group_id, 'secret' => $secret, 'object' => $object]);

        $user_id = $object['message']->from_id;

        $token = $_ENV['VK_TOKEN'];

        $user_info = json_decode(file_get_contents("https://api.vk.ru/method/users.get?user_ids={$user_id}&access_token={$token}&v=5.103"));

        //и извлекаем из ответа его имя
        $user_name = $user_info->response[0]->first_name;

        //С помощью messages.send отправляем ответное сообщение
        $request_params = array(
            'message' => "Hello, {$user_name}!",
            'peer_id' => $user_id,
            'access_token' => $token,
            'v' => '5.103',
            'random_id' => '0'
        );

        $get_params = http_build_query($request_params);

        file_get_contents('https://api.vk.ru/method/messages.send?' . $get_params);
    }
}
