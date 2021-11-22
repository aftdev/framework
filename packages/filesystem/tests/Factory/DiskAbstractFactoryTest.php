<?php

namespace AftDevTest\Filesystem\Factory;

use AftDev\Cache\CacheManager;
use AftDev\Filesystem\Cache;
use AftDev\Filesystem\Factory\DiskAbstractFactory;
use AftDev\ServiceManager\Resolver;
use AftDev\Test\TestCase;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Memory;
use League\Flysystem\Filesystem;
use Prophecy\Argument;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @covers \AftDev\Filesystem\Factory\DiskAbstractFactory
 */
class DiskAbstractFactoryTest extends TestCase
{
    /**
     * Test that the factory use the right information.
     */
    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);

        $container->has(Argument::any())->willReturn(false);

        $options = [
            'visibility' => 'private',
            'disable_asserts' => true,
            'extraOptionsA' => 'test',
            'extraOptionsB' => 'test2',
        ];

        $factory = new DiskAbstractFactory();

        $disk = $factory($container->reveal(), TestAdapter::class, $options);

        $this->assertInstanceOf(Filesystem::class, $disk);
        $diskConfig = $disk->getConfig();

        // Test that all the configurations were set properly.
        $this->assertSame(true, $diskConfig->get('case_sensitive'));
        $this->assertSame($options['visibility'], $diskConfig->get('visibility'));
        $this->assertSame($options['disable_asserts'], $diskConfig->get('disable_asserts'));

        // Make sure the adapter got the right options as well.
        $adapter = $disk->getAdapter();
        $this->assertSame($options['extraOptionsA'], $adapter->extraOptionsA);
        $this->assertSame($options['extraOptionsB'], $adapter->extraOptionsB);
        $this->assertSame('notset', $adapter->visibility);
    }

    public function testDiskWithCache()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has(Resolver::class)->willReturn(false);
        $cacheManager = $this->prophesize(CacheManager::class);

        $options = [
            'cache' => [
                'store' => 'test-store',
                'key' => 'test-key',
                'expire' => '3600',
            ],
        ];

        $container->has(CacheManager::class)->willReturn(true);
        $container->get(CacheManager::class)->willReturn($cacheManager->reveal());

        $storageMock = $this->prophesize(CacheItemPoolInterface::class);
        $cacheItem = $this->prophesize(CacheItemInterface::class);

        $cacheManager
            ->store($options['cache']['store'])
            ->shouldBeCalledOnce()
            ->willReturn($storageMock->reveal())
        ;

        $storageMock->getItem(Argument::any())->willReturn($cacheItem->reveal());
        $cacheItem->isHit()->willReturn(false);

        $factory = new DiskAbstractFactory();
        $cachedDisk = $factory($container->reveal(), NullAdapter::class, $options);

        $this->assertInstanceOf(Filesystem::class, $cachedDisk);
        $this->assertInstanceOf(CachedAdapter::class, $cachedDisk->getAdapter());
        $this->assertInstanceOf(NullAdapter::class, $cachedDisk->getAdapter()->getAdapter());
        $this->assertInstanceOf(Cache::class, $cachedDisk->getAdapter()->getCache());
    }

    /**
     * Test that cache=true config return memory cache adapter.
     */
    public function testInMemoryCache()
    {
        $container = $this->prophesize(ContainerInterface::class);

        $options = [
            'cache' => true,
        ];

        $factory = new DiskAbstractFactory();
        $cachedDisk = $factory($container->reveal(), NullAdapter::class, $options);

        $this->assertInstanceOf(Filesystem::class, $cachedDisk);
        $this->assertInstanceOf(CachedAdapter::class, $cachedDisk->getAdapter());
        $this->assertInstanceOf(NullAdapter::class, $cachedDisk->getAdapter()->getAdapter());
        $this->assertInstanceOf(Memory::class, $cachedDisk->getAdapter()->getCache());
    }

    /**
     * Test that exception is thrown if Cache Manager is not available.
     */
    public function testInvalidCacheConfig()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has(Resolver::class)->willReturn(false);
        $options = [
            'cache' => [
                'store not in config' => '',
            ],
        ];

        $container->has(CacheManager::class)->willReturn(true);

        $this->expectException(ServiceNotCreatedException::class);

        $factory = new DiskAbstractFactory();
        $factory($container->reveal(), NullAdapter::class, $options);
    }

    /**
     * Test that exception is thrown if Cache Manager is not available.
     */
    public function testDiskInvalidCacheManager()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has(Resolver::class)->willReturn(false);
        $options = [
            'cache' => [
                'store' => 'test-store',
            ],
        ];

        $container->has(CacheManager::class)->willReturn(false);

        $this->expectException(ServiceNotCreatedException::class);

        $factory = new DiskAbstractFactory();
        $factory($container->reveal(), NullAdapter::class, $options);
    }
}

class TestAdapter extends NullAdapter
{
    public $extraOptionsA;
    public $extraOptionsB;
    public $visibility;

    public function __construct($extraOptionsA, $extraOptionsB, $visibility = 'notset')
    {
        $this->extraOptionsA = $extraOptionsA;
        $this->extraOptionsB = $extraOptionsB;
        $this->visibility = $visibility;
    }
}
