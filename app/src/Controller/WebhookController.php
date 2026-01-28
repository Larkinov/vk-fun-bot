<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WebhookController extends AbstractController
{
    #[Route('/api/messages', name: 'vk_webhook')]
    public function index(): Response
    {
        return new Response('ok', 200, [
            'Content-Type' => 'text/plain'
        ]);
    }
}
