<?php

namespace AftDev\Filesystem\Factory;

use AftDev\Filesystem\ConfigProvider;
use AftDev\Filesystem\DiskManager;
use AftDev\ServiceManager\Factory\AbstractManagerFactory;
use Psr\Container\ContainerInterface;

class DiskManagerFactory extends AbstractManagerFactory
{
    protected $managerClass = DiskManager::class;

    /**
     * {@inheritdoc}
     */
    public function getManagerConfiguration(ContainerInterface $container): array
    {
        return $container->get('config')[ConfigProvider::CONFIG_KEY]['disks'];
    }
}
