<?php

declare(strict_types=1);

namespace AftDev\Cache;

use AftDev\ServiceManager\AbstractManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Contracts\Cache\CacheInterface;

class CacheManager extends AbstractManager
{
    protected $factories = [
        'chain' => Factory\ChainAdapterFactory::class,
        'tag_aware' => Factory\TagAwareAdapterFactory::class,
        Adapter\MemcachedAdapter::class => Factory\MemcachedAdapterFactory::class,
        Adapter\RedisAdapter::class => Factory\RedisAdapterFactory::class,
    ];

    protected $aliases = [
        'array' => Adapter\ArrayAdapter::class,
        'file' => Adapter\FilesystemAdapter::class,
        'php' => Adapter\PhpFilesAdapter::class,
        'php_array' => Adapter\PhpArrayAdapter::class,
        'redis' => Adapter\RedisAdapter::class,
        'memcached' => Adapter\MemcachedAdapter::class,
        'tag' => 'tag_aware',
    ];

    /**
     * Get the cache store adapter.
     *
     * @param null|string $store Name of the store to use
     *
     * @return CacheInterface|CacheItemPoolInterface
     */
    public function store(string $store = null)
    {
        if (null === $store) {
            return $this->getDefault();
        }

        return $this->getPlugin($store);
    }

    /**
     * Return a chain cache adapter.
     *
     * @param string[] $stores
     */
    public function chain(...$stores): Adapter\ChainAdapter
    {
        $stores = is_array($stores[0]) ? $stores[0] : $stores;
        $chainName = $this->createChain($stores);

        return $this->store($chainName);
    }

    public function validate($instance)
    {
        if ($instance instanceof CacheInterface || $instance instanceof CacheItemPoolInterface) {
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin manager "%s" expected an instance of type "%s", but "%s" was received',
            __CLASS__,
            CacheInterface::class.' or '.CacheItemPoolInterface::class,
            is_object($instance) ? get_class($instance) : gettype($instance)
        ));
    }

    /**
     * Transform a Psr6 Cache interface to a Psr16Cache.
     *
     * @see https://www.php-fig.org/psr/psr-16/#21-cacheinterface
     * @see https://www.php-fig.org/psr/psr-6/#cacheitempoolinterface
     */
    public static function psr16(CacheItemPoolInterface $psr6)
    {
        return new Psr16Cache($psr6);
    }

    /**
     * Create a chain plugin.
     *
     * @return string
     */
    protected function createChain(array $adapters)
    {
        $chainName = 'chain_'.md5(implode('-', $adapters));
        if (isset($this->pluginsOptions[$chainName])) {
            return $chainName;
        }

        $this->pluginsOptions[$chainName] = [
            'service' => 'chain',
            'options' => [
                'stores' => $adapters,
            ],
        ];

        return $chainName;
    }
}
