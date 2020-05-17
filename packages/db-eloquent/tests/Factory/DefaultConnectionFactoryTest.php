<?php

namespace AftDevTest\DbEloquent\Factory;

use AftDev\DbEloquent\Capsule\CapsuleManager;
use AftDev\DbEloquent\Factory\DefaultConnectionFactory;
use AftDev\Test\TestCase;
use Psr\Container\ContainerInterface;

/**
 * Class DefaultConnectionFactoryTest.
 *
 * @covers \AftDev\DbEloquent\Factory\DefaultConnectionFactory
 *
 * @internal
 */
class DefaultConnectionFactoryTest extends TestCase
{
    public function testMiddleware()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $capsuleManager = $this->prophesize(CapsuleManager::class);

        $container->get(CapsuleManager::class)->willReturn($capsuleManager->reveal());

        $capsuleManager->getConnection()->willReturn(true);

        $factory = new DefaultConnectionFactory();

        $test = $factory($container->reveal());
        $this->assertTrue($test);
    }
}
