<?php

namespace AftDev\Filesystem\Factory;

use AftDev\Cache\CacheManager;
use AftDev\Filesystem\Cache;
use AftDev\ServiceManager\Factory\ReflectionAbstractFactory;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Memory as MemoryStore;
use League\Flysystem\Filesystem;
use Psr\Container\ContainerInterface;

class DiskAbstractFactory extends ReflectionAbstractFactory
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $options ?? [];

        $filesystemConfig = [
            'visibility' => 'public',
            'case_sensitive' => true,
            'disable_asserts' => false,
        ];

        $cacheOptions = $options['cache'] ?? null;
        unset($options['cache']);

        // Fetch config of the filesystem.
        $filesystemConfig = array_intersect_key($options + $filesystemConfig, $filesystemConfig);

        // The rest are config for the adapter itself.
        $adapterConfig = array_diff_key($options, $filesystemConfig);

        // Build adapter.
        $adapter = parent::__invoke($container, $requestedName, $adapterConfig);

        // Cache ?
        if ($cacheOptions) {
            $adapter = $this->getCachedAdapter($container, $requestedName, $adapter, $cacheOptions);
        }

        // Return a filesystem.
        return new Filesystem($adapter, $filesystemConfig);
    }

    /**
     * Create a Cached Adapter.
     *
     * @param $requestedName
     * @param $adapter
     * @param array $cacheInfo
     */
    protected function getCachedAdapter(ContainerInterface $container, $requestedName, $adapter, $cacheInfo): CachedAdapter
    {
        // Memory cache.
        if (true === $cacheInfo) {
            return new CachedAdapter($adapter, new MemoryStore());
        }

        if (!$container->has(CacheManager::class)) {
            throw new ServiceNotCreatedException('Could not load the CacheManager');
        }

        $store = $cacheInfo['store'] ?? false;
        if (!$store) {
            throw new ServiceNotCreatedException('[store] config needed');
        }

        $expire = $cacheInfo['expire'] ?? null;
        $key = $cacheInfo['key'] ?? $requestedName.'_meta';

        $cacheManager = $container->get(CacheManager::class);
        $store = $cacheManager->store($store);

        $cacheAdapter = new Cache($store, $key, $expire);

        return new CachedAdapter($adapter, $cacheAdapter);
    }
}
