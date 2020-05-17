<?php

namespace AftDev\Filesystem\Factory;

use AftDev\Filesystem\DiskManager;
use AftDev\Filesystem\FileManager;
use AftDev\Filesystem\PluginManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FileManagerFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $diskManager = $container->get(DiskManager::class);
        $pluginManager = $container->get(PluginManager::class);

        return new FileManager([], $diskManager, $pluginManager);
    }
}
