<?php

namespace AftDev\Messenger\Sender;

use AftDev\Messenger\Queue\QueueManager;
use Psr\Container\ContainerInterface;

class QueueSenderFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $queueManager = $container->get(QueueManager::class);

        return new QueueSender($queueManager);
    }
}
