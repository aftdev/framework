<?php

namespace AftDevTest\Cache\Factory;

use AftDev\Cache\CacheManager;
use AftDev\Cache\Factory\ChainAdapterFactory;
use AftDev\Test\TestCase;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Prophecy\Argument;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

/**
 * @internal
 *
 * @covers \AftDev\Cache\Factory\ChainAdapterFactory
 */
class ChainAdapterFactoryTest extends TestCase
{
    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $cacheManager = $this->prophesize(CacheManager::class);

        $store = $this->prophesize(CacheItemPoolInterface::class);

        $container->get(CacheManager::class)->willReturn($cacheManager->reveal())->shouldBeCalledTimes(1);
        $cacheManager->store(Argument::any())->willReturn($store->reveal())->shouldBeCalledTimes(3);

        $factory = new ChainAdapterFactory();

        $factory($container->reveal(), 'chain', [
            'stores' => ['store_1', 'store_2', 'store_3'],
        ]);
    }

    public function testFactoryWithBadOptions()
    {
        $this->expectException(ServiceNotCreatedException::class);

        $container = $this->prophesize(ContainerInterface::class);

        $factory = new ChainAdapterFactory();
        $factory($container->reveal(), 'chain', []);
    }
}
