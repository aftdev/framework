<?php

namespace AftDev\Filesystem\Factory;

use AftDev\Filesystem\DiskManager;
use AftDev\Filesystem\FileManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class FileManagerFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $diskManager = $container->get(DiskManager::class);
        $allDisks = $diskManager->getAllDisks();

        // Build All file System
        return new FileManager($allDisks);
    }
}
