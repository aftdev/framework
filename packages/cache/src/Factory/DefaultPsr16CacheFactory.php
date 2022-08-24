<?php

namespace AftDev\Cache\Factory;

use AftDev\Cache\CacheManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Psr16Cache;

class DefaultPsr16CacheFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $cacheManager = $container->get(CacheManager::class);

        $psr6 = $cacheManager->getDefault();

        return new Psr16Cache($psr6);
    }
}
