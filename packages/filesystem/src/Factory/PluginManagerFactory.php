<?php

namespace AftDev\Filesystem\Factory;

use AftDev\Filesystem\ConfigProvider;
use AftDev\Filesystem\PluginManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class PluginManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config')[ConfigProvider::CONFIG_KEY]['plugin_manager'] ?? [];

        return new PluginManager($config);
    }
}
