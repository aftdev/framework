<?php

namespace AftDev\Messenger\Queue;

use AftDev\Messenger\ConfigProvider;
use AftDev\ServiceManager\Factory\AbstractManagerFactory;
use Psr\Container\ContainerInterface;

class QueueManagerFactory extends AbstractManagerFactory
{
    protected $managerClass = QueueManager::class;

    /**
     * {@inheritdoc}
     */
    public function getManagerConfiguration(ContainerInterface $container): array
    {
        return $container->get('config')[ConfigProvider::KEY_MESSENGER][ConfigProvider::KEY_QUEUES];
    }
}
