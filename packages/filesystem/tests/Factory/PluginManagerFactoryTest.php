<?php

namespace AftDevTest\Filesystem\Factory;

use AftDev\Filesystem\ConfigProvider;
use AftDev\Filesystem\Factory\PluginManagerFactory;
use AftDev\Filesystem\FileManager;
use AftDev\Filesystem\PluginManager;
use AftDev\Test\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @covers \AftDev\Filesystem\Factory\PluginManagerFactory
 */
class PluginManagerFactoryTest extends TestCase
{
    /**
     * Test that the factory use the right information.
     */
    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);

        $config = $this->prophesize(\ArrayObject::class);

        $config
            ->offsetExists(ConfigProvider::CONFIG_KEY)
            ->shouldBeCalled()
            ->willReturn(true)
        ;

        $config
            ->offsetGet(ConfigProvider::CONFIG_KEY)
            ->shouldBeCalled()
            ->willReturn([
                'plugin_manager' => [],
            ])
        ;

        $container->get('config')->willReturn($config->reveal());

        $factory = new PluginManagerFactory();
        $pluginManager = $factory($container->reveal(), FileManager::class);

        $this->assertInstanceOf(PluginManager::class, $pluginManager);
    }
}
