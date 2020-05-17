<?php

namespace AftDev\Messenger\Factory;

use AftDev\Messenger\Messenger;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $commandBus = $container->get(MessageBusInterface::class);

        return new Messenger($commandBus);
    }
}
