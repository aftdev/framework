<?php

namespace AftDev\Cache;

use AftDev\ServiceManager\Factory\ReflectionAbstractFactory;
use Psr\Cache\CacheItemPoolInterface;
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
            ],
        ];
    }

    public function getCacheManagerConfig()
    {
        return [
            'default' => 'filesystem',
            'default_options' => [
                'namespace' => 'application',
                'defaultLifetime' => 3600,
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
                ],
            ],
            'abstract_factories' => [
                'default' => ReflectionAbstractFactory::class,
            ],
        ];
    }
}
