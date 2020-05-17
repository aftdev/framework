<?php

namespace AftDev\Messenger\Factory;

use AftDev\Messenger\ConfigProvider;
use Laminas\Stdlib\SplPriorityQueue;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\MessageBus;

class MessageBusFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $middlewares = $container->get('config')[ConfigProvider::KEY_MESSENGER][ConfigProvider::KEY_MIDDLEWARES];

        $list = new SplPriorityQueue();

        foreach ($middlewares as $name => $middlewareInfo) {
            $middleware = $middlewareInfo[0] ?? $middlewareInfo;
            $priority = $middlewareInfo[1] ?? 0;

            $middlewareClass = $container->has($middleware) ? $container->get($middleware) : new $middleware();

            $list->insert($middlewareClass, $priority);
        }

        return new MessageBus($list->toArray());
    }
}
