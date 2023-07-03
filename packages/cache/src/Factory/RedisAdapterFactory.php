<?php

namespace AftDev\Cache\Factory;

use AftDev\ServiceManager\Factory\ResolverAbstractFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisAdapterFactory extends ResolverAbstractFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Create RedisClient Client.
        [$servers, $redisOptions] = $this->getConnectionOptions($options);
        $options['redis'] = RedisAdapter::createConnection($servers, $redisOptions);

        return parent::__invoke($container, RedisAdapter::class, $options);
    }

    /**
     * Fetch Connection Values from the given options.
     */
    public function getConnectionOptions(array $options = []): array
    {
        $servers = $options['servers'] ?? $options['server'] ?? 'redis://localhost:6379';
        $redisOptions = $options['redis_options'] ?? $options['options'] ?? [];

        $connectionOptions['servers'] = $servers;
        $connectionOptions['options'] = $redisOptions;

        return [$servers, $redisOptions];
    }
}
