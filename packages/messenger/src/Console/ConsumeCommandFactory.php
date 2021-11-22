<?php

namespace AftDev\Messenger\Console;

use AftDev\Messenger\Queue\QueueManager;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Messenger\Command\ConsumeMessagesCommand;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\RoutableMessageBus;

class ConsumeCommandFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $commandBus = $container->get(MessageBusInterface::class);

        $routableBus = new RoutableMessageBus($container, $commandBus);
        $transportManager = $container->get(QueueManager::class);

        $logger = $container->get(LoggerInterface::class);

        $eventDispatcher = new EventDispatcher();

        return new ConsumeMessagesCommand($routableBus, $transportManager, $eventDispatcher, $logger);
    }
}
