<?php

namespace App\Infrastructure\Controller;

use App\Infrastructure\Requests\VkRequest;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WebhookController extends AbstractController
{

    public function __construct(
        private VkRequest $request,
        private LoggerInterface $logger,
    ) {}

    #[Route('/api/messages', name: 'vk_webhook')]
    public function index(): Response
    {
        $this->logger->info('start working app');

        try {
            $data = json_decode(file_get_contents('php://input'));
            $this->request->parse($data);
        } catch (\Throwable $th) {
            $this->logger->error($th->getMessage(), ['trace' => $th->getTrace()]);
        }

        $this->logger->info('end working app');
        return new Response('ok', 200, [
            'Content-Type' => 'text/plain'
        ]);
    }
}
