<?php

namespace AftDevTest\Cache\Factory;

use AftDev\Cache\CacheManager;
use AftDev\Cache\Factory\DefaultCacheFactory;
use AftDev\Test\TestCase;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @coversNothing
 */
class DefaultCacheFactoryTest extends TestCase
{
    /**
     * Test that factory return default value.
     */
    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $cacheManager = $this->prophesize(CacheManager::class);

        $container->get(Argument::any())->willReturn($cacheManager->reveal());
        $cacheManager->getDefault()->willReturn(true);

        $factory = new DefaultCacheFactory();
        $defaultAdapter = $factory($container->reveal(), 'test');

        $this->assertTrue($defaultAdapter);
    }
}
