<?php

namespace App\Service;

use App\Fabric\FabricCommand;
use App\Fabric\FabricMessageVK;
use App\ValueObject\VK\CommandVK;
use App\ValueObject\VK\MessageVK;
use Generator\Skeleton\skeleton\base\src\VK\CallbackApi\VKCallbackApiServerHandler;
use Psr\Log\LoggerInterface;

class ServerVkHandler extends VKCallbackApiServerHandler
{

    public function __construct(
        private LoggerInterface $logger,
        private FabricMessageVK $fabricMessageVk,
        private FabricCommand $fabricCommand,
    ) {}

    public function messageNew(int $group_id, ?string $secret, array $object): void
    {
        $this->logger->info('message new', ['id' => $group_id, 'secret' => $secret, 'object' => $object]);

        try {
            $messageVk = $this->fabricMessageVk->getInstance($object);

            if (!MessageVK::isCorrectCommand($this->logger, $messageVk->getText()))
                return;

            $command = $this->fabricCommand->getInstance($messageVk);
        } catch (\Throwable $th) {
            $this->logger->error('failed create new command', ['message' => $th->getMessage(), 'trace' => $th->getTrace()]);
        }



        $user_id = $object['message']->from_id;

        $token = $_ENV['VK_TOKEN'];

        // $user_info = json_decode(file_get_contents("https://api.vk.ru/method/users.get?user_ids={$user_id}&access_token={$token}&v=5.103"));

        // //и извлекаем из ответа его имя
        // $user_name = $user_info->response[0]->first_name;

        // //С помощью messages.send отправляем ответное сообщение
        $request_params = array(
            'message' => "Hello, ты!" . $messageVk->getText(),
            'peer_id' => $user_id,
            'access_token' => $token,
            'v' => '5.103',
            'random_id' => '0'
        );

        $get_params = http_build_query($request_params);

        file_get_contents('https://api.vk.ru/method/messages.send?' . $get_params);
    }
}
