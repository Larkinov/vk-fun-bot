<?php

namespace App\Controller;

use App\Service\ServerVkHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WebhookController extends AbstractController
{

    public function __construct(
        private ServerVkHandler $handler
    ) {}

    #[Route('/api/messages', name: 'vk_webhook')]
    public function index(): Response
    {
        $data = json_decode(file_get_contents('php://input'));
        $this->handler->parse($data);

        return new Response('ok', 200, [
            'Content-Type' => 'text/plain'
        ]);
    }
}
