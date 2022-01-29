<?php

namespace AftDev\Filesystem\Factory;

use AftDev\ServiceManager\Factory\ReflectionAbstractFactory;
use League\Flysystem\Filesystem;
use Psr\Container\ContainerInterface;

class DiskAbstractFactory extends ReflectionAbstractFactory
{
    use GetConfigTrait;

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Fetch config of the filesystem.
        [$filesystemConfig, $adapterConfig] = $this->getConfigFromOptions($options ?? []);

        // Build adapter.
        $adapter = parent::__invoke($container, $requestedName, $adapterConfig);

        // Return a filesystem.
        return new Filesystem($adapter, $filesystemConfig);
    }
}
