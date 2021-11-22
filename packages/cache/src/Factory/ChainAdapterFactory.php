<?php

namespace AftDev\Cache\Factory;

use AftDev\Cache\CacheManager;
use AftDev\ServiceManager\Factory\ReflectionAbstractFactory;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ChainAdapter;

class ChainAdapterFactory extends ReflectionAbstractFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $options ?? [];

        /** @var CacheManager $cacheManager */
        $cacheManager = $container->get(CacheManager::class);

        $storeNames = $options['stores'] ?? [];

        if (empty($storeNames)) {
            throw new ServiceNotCreatedException('Stores options is missing.');
        }

        $stores = [];
        foreach ($storeNames as $adapter) {
            $stores[] = $cacheManager->store($adapter);
        }

        $lifetime = $options['lifetime'] ?? 0;

        return new ChainAdapter($stores, $lifetime);
    }
}
