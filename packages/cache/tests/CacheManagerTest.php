<?php

namespace AftDevTest\Cache;

use AftDev\Cache\CacheManager;
use AftDev\Test\TestCase;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Prophecy\Argument;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ChainAdapter;

/**
 * @internal
 * @covers \AftDev\Cache\CacheManager
 */
class CacheManagerTest extends TestCase
{
    public function testChain()
    {
        $container = $this->prophesize(ContainerInterface::class);

        $managerConfig = [
            'default' => 'store_1',
            'default_options' => [
                'namespace' => 'application',
            ],
            'plugins' => [
                'chain' => [
                    'service' => 'chain',
                    'options' => [
                        'stores' => ['store_1', 'store_2'],
                    ],
                ],
                'store_1' => [
                    'service' => 'item_pool',
                    'options' => ['a' => 'a'],
                ],
                'store_2' => [
                    'service' => 'item_pool',
                    'options' => ['b' => 'b'],
                ],
                'array' => [
                ],
            ],
            'factories' => [
                'item_pool' => function () {
                    $store = $this->prophesize(CacheItemPoolInterface::class);

                    return $store->reveal();
                },
            ],
            'shared_by_default' => false,
        ];

        $manager = new CacheManager($container->reveal(), $managerConfig);

        $container->get(CacheManager::class)->willReturn($manager);
        $container->has(Argument::any())->willReturn(false);

        $default = $manager->store();

        $store = $manager->store('store_1');
        $this->assertInstanceOf(CacheItemPoolInterface::class, $store);
        $this->assertSame($default, $store);

        $chain = $manager->chain('store_1', 'store_2');
        $this->assertInstanceOf(ChainAdapter::class, $chain);

        $chainWithSameStores = $manager->chain('store_1', 'store_2');
        $this->assertSame($chainWithSameStores, $chain);
    }

    /**
     * Make sure invalid services throw an error.
     */
    public function testInvalidService()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $managerConfig = [
            'plugins' => [
                'anything' => [
                    'service' => 'item_pool',
                ],
            ],
            'factories' => [
                'item_pool' => function () {
                    return new \stdClass();
                },
            ],
        ];

        $this->expectException(InvalidServiceException::class);

        $manager = new CacheManager($container->reveal(), $managerConfig);
        $manager->store('anything');
    }
}
