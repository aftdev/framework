<?php

namespace AftDev\Filesystem;

use League\Flysystem\Cached\Storage\AbstractCache;
use Psr\Cache\CacheItemPoolInterface;

class Cache extends AbstractCache
{
    /**
     * The cache repository implementation.
     *
     * @var \Psr\Cache\CacheItemPoolInterface
     */
    protected $store;

    /**
     * The cache key.
     *
     * @var string
     */
    protected $key;

    /**
     * The cache expiration time in minutes.
     *
     * @var int
     */
    protected $expire;

    /**
     * Create a new cache instance.
     *
     * @param string $key
     */
    public function __construct(CacheItemPoolInterface $store, $key = 'flysystem', int $expire = null)
    {
        $this->store = $store;
        $this->key = $key;
        $this->expire = $expire;
    }

    /**
     * Load the cache.
     */
    public function load()
    {
        $item = $this->store->getItem($this->key);

        if ($item->isHit()) {
            $this->setFromStorage($item->get());
        }
    }

    /**
     * Persist the cache.
     */
    public function save()
    {
        $contents = $this->getForStorage();

        $item = $this->store->getItem($this->key);
        $item->set($contents);

        if ($this->expire) {
            $item->expiresAfter($this->expire);
        }

        $this->store->save($item);
    }
}
