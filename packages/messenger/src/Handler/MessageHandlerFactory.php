<?php

namespace AftDev\Messenger\Handler;

use AftDev\ServiceManager\Resolver;
use Psr\Container\ContainerInterface;

class MessageHandlerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $resolver = $container->get(Resolver::class);

        return new MessageHandler($resolver);
    }
}
