<?php

namespace AftDevTest\Filesystem;

use AftDev\Filesystem\Cache;
use AftDev\Test\TestCase;
use Prophecy\Argument;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @internal
 * @covers \AftDev\Filesystem\Cache
 */
class CacheTest extends TestCase
{
    // Test cache adapter.
    public function testLoad()
    {
        $cacheAdapter = $this->prophesize(CacheItemPoolInterface::class);
        $itemMock = $this->prophesize(CacheItemInterface::class);

        $itemKey = 'test';
        $expire = 500;

        $cacheAdapter
            ->getItem($itemKey)
            ->shouldBeCalledTimes(2)
            ->willReturn($itemMock->reveal())
        ;

        $itemMock->isHit()
            ->shouldBeCalledTimes(2)
            ->willReturn(true, false)
        ;

        $cacheContent = json_encode([[], []]);
        $itemMock->get()
            ->shouldBeCalledTimes(1)
            ->willReturn($cacheContent)
        ;

        $cache = new Cache($cacheAdapter->reveal(), $itemKey, $expire);

        // With cache content.
        $cache->load();

        // Without content.
        $cache = new Cache($cacheAdapter->reveal(), $itemKey, $expire);
        $cache->load();
    }

    /**
     * Test the save function.
     */
    public function testSave()
    {
        $cacheAdapter = $this->prophesize(CacheItemPoolInterface::class);
        $itemMock = $this->prophesize(CacheItemInterface::class);
        $itemMockNoExpiry = $this->prophesize(CacheItemInterface::class);

        $itemKey = 'test';
        $expire = 500;

        $cacheAdapter
            ->getItem($itemKey)
            ->shouldBeCalledTimes(2)
            ->willReturn($itemMock->reveal(), $itemMockNoExpiry->reveal())
        ;

        $cache = new Cache($cacheAdapter->reveal(), $itemKey, $expire);
        $storageData = $cache->getForStorage();

        $itemMock->expiresAfter($expire)->shouldBeCalledOnce();
        $itemMock->set($storageData)->shouldBeCalledOnce();
        $cacheAdapter->save($itemMock)->shouldBeCalledOnce();

        $cache->save();

        $noExpiry = new Cache($cacheAdapter->reveal(), $itemKey);

        $itemMockNoExpiry->set($storageData)->shouldBeCalledOnce();
        $itemMockNoExpiry->expiresAfter(Argument::any())->shouldNotHaveBeenCalled();
        $cacheAdapter->save($itemMockNoExpiry)->shouldBeCalledOnce();

        $noExpiry->save();

        $cacheAdapter->save(Argument::any())->shouldHaveBeenCalledTimes(2);
    }
}
