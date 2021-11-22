<?php

namespace AftDev\Test\Feature\Cache;

use AftDev\Cache\CacheManager;
use AftDev\Test\FeatureTestCase;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @internal
 * @coversNothing
 */
class CacheAdapterTest extends FeatureTestCase
{
    /**
     * @var CacheManager
     */
    protected $cacheManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->cacheManager = $this->container->get(CacheManager::class);
    }

    /**
     * Test Default cache.
     *
     * @covers \AftDev\Cache\Factory\DefaultCacheFactory
     */
    public function testDefaultCache()
    {
        $defaultCache = $this->container->get(CacheInterface::class);
        $this->assertInstanceOf(CacheInterface::class, $defaultCache);

        $defaultCachePoolItem = $this->container->get(CacheItemPoolInterface::class);
        $this->assertInstanceOf(CacheItemPoolInterface::class, $defaultCachePoolItem);
    }

    /**
     * @dataProvider adapterProviders
     *
     * @covers \AftDev\Cache\Factory\MemcachedAdapterFactory
     * @covers \AftDev\Cache\Factory\RedisAdapterFactory
     */
    public function testAdapters(string $store = null, string $expected)
    {
        $cache = $this->cacheManager->store($store);

        $this->assertInstanceOf($expected, $cache);
        $key = 'sameKey';

        $item = $cache->getItem($key);
        $this->assertFalse($item->isHit());

        $item->set('VALUE A');
        $cache->save($item);

        $itemB = $cache->getItem($key);
        $this->assertTrue($itemB->isHit());

        $this->assertSame($item->get(), $itemB->get());

        $cache->delete($key);
    }

    public function adapterProviders()
    {
        return [
            'default' => [
                'store' => null,
                'expected' => ArrayAdapter::class,
            ],
            'array' => [
                'store' => 'array',
                'expected' => ArrayAdapter::class,
            ],
            'memcached' => [
                'store' => 'memcached',
                'expected' => MemcachedAdapter::class,
            ],
            'redis' => [
                'store' => 'redis',
                'expected' => RedisAdapter::class,
            ],
        ];
    }
}
