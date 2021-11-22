<?php

namespace AftDevTest\Cache\Factory;

use AftDev\Cache\CacheManager;
use AftDev\Cache\Factory\TagAwareAdapterFactory;
use AftDev\Test\TestCase;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @internal
 * @covers \AftDev\Cache\Factory\TagAwareAdapterFactory
 */
class TagAwareAdapterFactoryTest extends TestCase
{
    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $cacheManager = $this->prophesize(CacheManager::class);

        $store = $this->prophesize(AdapterInterface::class);
        $store_2 = $this->prophesize(AdapterInterface::class);

        $container->get(CacheManager::class)->willReturn($cacheManager->reveal())->shouldBeCalledTimes(1);
        $cacheManager->store('store_1')->willReturn($store->reveal())->shouldBeCalledTimes(1);
        $cacheManager->store('store_2')->willReturn($store_2->reveal())->shouldBeCalledTimes(1);

        $factory = new TagAwareAdapterFactory();

        $factory($container->reveal(), 'taggeable', [
            'itemsPool' => 'store_1',
            'tagsPool' => 'store_2',
        ]);
    }

    public function testFactoryWithBadOptions()
    {
        $this->expectException(ServiceNotCreatedException::class);

        $container = $this->prophesize(ContainerInterface::class);

        $factory = new TagAwareAdapterFactory();
        $factory($container->reveal(), 'chain', []);
    }
}
