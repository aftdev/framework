<?php

declare(strict_types=1);

namespace AftDev\Api\Factory;

use AftDev\Cache\CacheManager;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

trait InteractsWithCacheTrait
{
    public function getCache(ContainerInterface $container, iterable $config): ?CacheItemPoolInterface
    {
        $store = $config['cache'] ?? null;
        if ($store && $container->has(CacheManager::class)) {
            return $container->get(CacheManager::class)->store($store);
        }

        return null;
    }
}
