<?php

namespace AftDev\Cache;

use AftDev\ServiceManager\Factory\ResolverAbstractFactory;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface as SimpleCacheInterface;
use Symfony\Contracts\Cache\CacheInterface;

class ConfigProvider
{
    public function __invoke()
    {
        $config['dependencies'] = $this->getDependencyConfig();
        $config['cache'] = $this->getCacheManagerConfig();

        return $config;
    }

    public function getDependencyConfig()
    {
        return [
            'factories' => [
                CacheManager::class => Factory\CacheManagerFactory::class,
                CacheInterface::class => Factory\DefaultCacheFactory::class,
                CacheItemPoolInterface::class => Factory\DefaultCacheFactory::class,
                SimpleCacheInterface::class => Factory\DefaultPsr16CacheFactory::class,
            ],
        ];
    }

    public function getCacheManagerConfig()
    {
        return [
            'default' => 'filesystem',
            'default_options' => [
                'namespace' => 'application',
                'defaultLifetime' => 2 * 60 * 60, // 2 Hours
            ],
            'plugins' => [
                'filesystem' => [
                    'service' => 'file',
                    'options' => [
                        'directory' => 'data/cache',
                    ],
                ],
                'php' => [
                    'directory' => 'data/cache',
                    'namespace' => 'php',
                    'defaultLifetime' => 0,
                    'appendOnly' => true,
                ],
            ],
            'abstract_factories' => [
                'default' => ResolverAbstractFactory::class,
            ],
        ];
    }
}
