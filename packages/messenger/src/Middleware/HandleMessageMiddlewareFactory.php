<?php

namespace AftDev\Messenger\Middleware;

use AftDev\Messenger\ConfigProvider;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

class HandleMessageMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $handlers = $container->get('config')[ConfigProvider::KEY_MESSENGER][ConfigProvider::KEY_HANDLERS];

        $fullyLoadedHandlers = [];
        foreach ($handlers as $type => $typeHandlers) {
            foreach ($typeHandlers as $typeHandlerName) {
                $typeHandler = $typeHandlerName;
                if (!is_callable($typeHandlerName)) {
                    $typeHandler = $container->has($typeHandlerName) ? $container->get($typeHandlerName) : new $typeHandlerName();
                }

                $fullyLoadedHandlers[$type][] = $typeHandler;
            }
        }

        $handlerLocator = new HandlersLocator($fullyLoadedHandlers);

        $middleware = new HandleMessageMiddleware($handlerLocator);

        if ($container->has(LoggerInterface::class)) {
            $middleware->setLogger($container->get(LoggerInterface::class));
        }

        return $middleware;
    }
}
