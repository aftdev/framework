<?php

namespace AftDevTest\Filesystem\Factory;

use AftDev\Filesystem\DiskManager;
use AftDev\Filesystem\Factory\FileManagerFactory;
use AftDev\Filesystem\FileManager;
use AftDev\Filesystem\PluginManager;
use AftDev\Test\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @covers \AftDev\Filesystem\Factory\FileManagerFactory
 */
class FileManagerFactoryTest extends TestCase
{
    /**
     * Test that the factory use the right information.
     */
    public function testFactory()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $diskManager = $this->prophesize(DiskManager::class);
        $pluginManager = $this->prophesize(PluginManager::class);

        $container->get(DiskManager::class)->willReturn($diskManager->reveal());
        $container->get(PluginManager::class)->willReturn($pluginManager->reveal());

        $factory = new FileManagerFactory();
        $filemanager = $factory($container->reveal(), FileManager::class);

        $this->assertInstanceOf(FileManager::class, $filemanager);
    }
}
