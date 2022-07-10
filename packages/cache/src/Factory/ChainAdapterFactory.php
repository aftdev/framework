<?php

namespace AftDev\Cache\Factory;

use AftDev\Cache\CacheManager;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ChainAdapter;

class ChainAdapterFactory implements FactoryInterface
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
