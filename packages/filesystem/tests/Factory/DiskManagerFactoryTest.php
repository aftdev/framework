<?php

namespace AftDevTest\Filesystem\Factory;

use AftDev\Filesystem\ConfigProvider;
use AftDev\Filesystem\Factory\DiskManagerFactory;
use AftDev\Test\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @covers \AftDev\Filesystem\Factory\DiskManagerFactory
 */
class DiskManagerFactoryTest extends TestCase
{
    /**
     * Test that the factory use the right information.
     */
    public function testConfig()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $diskConfig = ['a', 'b'];
        $container->get('config')->willReturn(
            [
                ConfigProvider::CONFIG_KEY => [
                    'disks' => $diskConfig,
                ],
            ]
        );
        $factory = new DiskManagerFactory();

        $this->assertSame($diskConfig, $factory->getManagerConfiguration($container->reveal()));
    }
}
