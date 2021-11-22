<?php

namespace AftDev\Cache\Factory;

use AftDev\Cache\CacheManager;
use Psr\Container\ContainerInterface;

class DefaultCacheFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $cacheManager = $container->get(CacheManager::class);

        return $cacheManager->getDefault();
    }
}
