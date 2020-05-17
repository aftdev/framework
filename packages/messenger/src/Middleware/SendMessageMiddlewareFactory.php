<?php

namespace AftDev\Messenger\Middleware;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;

class SendMessageMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $sendersLocator = $container->get(SendersLocator::class);

        return new SendMessageMiddleware($sendersLocator);
    }
}
