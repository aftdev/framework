<?php

namespace AftDev\Cache\Factory;

use AftDev\ServiceManager\Factory\ResolverAbstractFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

class MemcachedAdapterFactory extends ResolverAbstractFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        [$servers, $memCachedOptions] = $this->getConnectionOptions($options);

        // Create clients
        $options['client'] = MemcachedAdapter::createConnection($servers, $memCachedOptions);

        // Create Memcached Cache Adapter.
        return parent::__invoke($container, MemcachedAdapter::class, $options);
    }

    /**
     * Fetch Connection Values from the given options.
     */
    public function getConnectionOptions(array $options = []): array
    {
        $servers = $options['servers'] ?? $options['server'] ?? 'memcached://localhost:11222';
        $memcachedOptions = $options['memcached_options'] ?? $options['options'] ?? [];

        $connectionOptions['servers'] = $servers;
        $connectionOptions['options'] = $memcachedOptions;

        return [$servers, $memcachedOptions];
    }
}
